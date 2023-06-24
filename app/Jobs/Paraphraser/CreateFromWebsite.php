<?php

namespace App\Jobs\Paraphraser;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateFromWebsite
{
    use Dispatchable, SerializesModels;

    public Document $document;
    public array $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $repo = new DocumentRepository($this->document);

        $repo->createTask(
            DocumentTaskEnum::CRAWL_WEBSITE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => ['parse_sentences' => true],
                'order' => 2
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);

        // $repo->createTask(
        //     DocumentTaskEnum::CRAWL_WEBSITE,
        //     [
        //         'process_id' => $this->params['process_id'],
        //         'meta' => [],
        //         'order' => 3
        //     ]
        // );

        // $repo->createTask(
        //     DocumentTaskEnum::PARAPHRASE_TEXT,
        //     [
        //         'process_id' => $this->params['process_id'],
        //         'meta' => [
        //             'platform' => $this->params['platform'],
        //         ],
        //         'order' => 4
        //     ]
        // );
    }
}
