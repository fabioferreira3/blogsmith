<?php

namespace App\Jobs;

use App\Enums\DataType;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Packages\Oraculum\Oraculum;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class EmbedSource implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public DataType $dataType;
    public string $source;
    public string $collectionName;
    public array $meta;


    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 7;

    /**
     * How many seconds Laravel should wait before retrying a job that has encountered an exception
     *
     * @var int
     */
    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [3, 7, 15];
    }

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
    {
        $this->document = $document->fresh();
        $this->dataType = DataType::tryFrom($meta['data_type']);
        $this->source = $meta['source'];
        $this->collectionName = $meta['collection_name'] ?? $document->id;
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
            if (in_array($this->dataType, [
                DataType::PDF,
                DataType::DOCX,
                DataType::CSV,
                //    DataType::JSON
            ], true)) {
                $expirationDate = now()->addMinutes(15);
                $tempUrl = Storage::temporaryUrl($this->source, $expirationDate);
                // $shortLink = SupportHelper::shortenLink($tempUrl, [
                //     'account_id' => $this->document->account_id,
                //     'expires_at' => $expirationDate
                // ]);
                $shortLink = app('bitly')->getUrl($tempUrl);
                $this->source = $shortLink;
            }
            $user = User::findOrFail($this->document->getMeta('user_id'));
            $oraculum = new Oraculum($user, $this->collectionName);
            $oraculum->add($this->dataType, $this->source);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed($e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'embed_document_' . $this->dataType->value . '_' . $this->document->id;
    }
}