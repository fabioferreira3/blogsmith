<?php

namespace App\Jobs;

use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpandOutline implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = new PromptHelper($document->language->value);
        $this->repo = new DocumentRepository($this->document);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $chatGpt = new ChatGPT();

            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' => $this->promptHelper->writeFirstPass($this->document->meta['outline'], $this->meta['tone'])
                ]
            ]);
            $this->repo->updateMeta('raw_structure', DocumentHelper::parseHtmlTagsToRawStructure($response['content']));
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand outline: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'expand_outline_' . $this->meta['process_id'] ?? $this->document->id;
    }
}