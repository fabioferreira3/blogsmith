<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Jobs\DispatchDocumentTasks;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateSocialMediaPost
{
    use Dispatchable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = [
            ...$params,
            'process_id' => Str::uuid(),
            'type' => DocumentType::SOCIAL_MEDIA_POST->value
        ];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->createSocialMediaPost($this->params);
        $this->repo->setDocument($document);
        $this->repo->createTask(DocumentTaskEnum::SUMMARIZE_DOC, [
            'order' => 1,
            'process_id' => $this->params['process_id']
        ]);

        $document->refresh();

        if ($this->params['source'] === 'website_url') {
            $this->repo->createTask(
                DocumentTaskEnum::CRAWL_WEBSITE,
                [
                    'process_id' => $this->params['process_id'],
                    'meta' => [],
                    'order' => 2
                ]
            );
        }

        $platforms = collect($document->meta['platforms'])
            ->filter(function ($value) {
                return $value;
            })->keys();
        $platforms->each(function ($platform, $index) {
            $this->repo->createTask(
                DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
                [
                    'process_id' => $this->params['process_id'],
                    'meta' => [
                        'platform' => $platform,
                    ],
                    'order' => 3 + $index
                ]
            );
        });

        $this->repo->createTask(
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => 99
            ]
        );

        $this->repo->createTask(DocumentTaskEnum::REGISTER_FINISHED_PROCESS, [
            'order' => 100,
            'process_id' => $this->params['process_id'],
            'meta' => []
        ]);

        DispatchDocumentTasks::dispatch($document);
    }
}
