<?php

namespace App\Jobs;

use App\Enums\DataType;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProcessAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->meta['identify_speakers'] ?? false) {
                MediaRepository::transcribeAudioWithDiarization(
                    $this->document->getMeta('audio_file_path'),
                    [
                        'document_id' => $this->document->id,
                        'task_id' => $this->meta['task_id']
                    ]
                );
                $this->jobPending();
                return;
            } else {
                $transcribedText = MediaRepository::transcribeAudio(
                    $this->document->getMeta('audio_file_path')
                );

                if ($this->meta['embed_source'] ?? false) {
                    EmbedSource::dispatchSync($this->document, [
                        'data_type' => DataType::TEXT->value,
                        'source' => $transcribedText
                    ]);
                }

                $this->document->update([
                    'meta' => [
                        ...$this->document->meta,
                        'context' => $transcribedText,
                        'original_text' => $transcribedText
                    ]
                ]);
            }

            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Audio processing error: ' . $e->getMessage());
        }
    }

    protected function compressAndStoreMp3($localFile, $fileName)
    {
        $maxFileSize = 22 * 1024 * 1024; // 25 MB in bytes

        // Check if the file size is greater than 25 MB
        if ($localFile->getSize() > $maxFileSize) {
            // Compress the file using FFmpeg
            $inputPath = $localFile->getRealPath();
            $outputPath = Storage::disk('local')->path('compressed_' . $fileName);

            $process = new Process([
                'ffmpeg',
                '-i', $inputPath,
                '-codec:a', 'libmp3lame',
                '-b:a', '48k', // You can adjust the bitrate to control the output file size and quality
                $outputPath
            ]);

            $process->setTimeout(0);
            $process->run();

            // Check if the compression was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Replace the original file with the compressed one
            $newLocalFile = Storage::disk('local')->get('compressed_' . $fileName);

            // Store the file using the Storage facade
            Storage::disk('local')->put($fileName, $newLocalFile);
            return true;
        }

        return false;
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'process_audio_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
