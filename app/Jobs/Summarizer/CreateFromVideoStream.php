<?php

namespace App\Jobs\Summarizer;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateFromVideoStream implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Document $document;
    public array $params;
    public string $processId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->processId = $params['process_id'] ?? Str::uuid();
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'source_url' => $this->document->getMeta('source_url')
                ],
                'order' => 1
            ]
        );
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::TRANSCRIBE_AUDIO,
            [
                'process_id' => $this->processId,
                'order' => 2
            ]
        );
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::PUBLISH_TEXT_BLOCK,
            [
                'process_id' => $this->processId,
                'order' => 3,
                'meta' => [
                    'text' => $this->document->getMeta('context'),
                    'target_language' => $this->document->getMeta('target_language') ?? null
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($this->document);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_summary_from_video_stream_' . $this->document->id;
    }
}
