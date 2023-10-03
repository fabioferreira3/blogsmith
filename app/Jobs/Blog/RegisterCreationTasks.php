<?php

namespace App\Jobs\Blog;

use App\Enums\DocumentTaskEnum;
use App\Helpers\MediaHelper;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Talendor\StabilityAI\Enums\StylePreset;

class RegisterCreationTasks
{
    use Dispatchable, SerializesModels;

    public Document $document;
    protected $repo;
    protected array $params;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
        $this->repo = new DocumentRepository($document);
    }

    public function handle()
    {
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::SUMMARIZE_DOC,
            [
                'order' => $this->params['next_order'],
                'process_id' => $this->params['process_id']
            ]
        );

        if ($this->params['meta']['generate_image'] ?? false) {
            $processId = Str::uuid();
            $imageSize = MediaHelper::getPossibleImageSize($this->document);
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::GENERATE_IMAGE,
                [
                    'order' => 1,
                    'process_id' => $processId,
                    'meta' => [
                        'prompt' => $this->params['meta']['img_prompt'],
                        'height' => $imageSize['height'],
                        'width' => $imageSize['width'],
                        'style_preset' => StylePreset::DIGITAL_ART->value,
                        'steps' => 21,
                        'samples' => 1,
                        'add_content_block' => true
                    ]
                ]
            );
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
                [
                    'order' => 2,
                    'process_id' => $processId,
                    'meta' => [
                        'silently' => true
                    ]
                ]
            );
        }

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::SUMMARIZE_DOC,
            [
                'order' => $this->params['next_order'],
                'process_id' => $this->params['process_id']
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_OUTLINE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => $this->params['next_order'] + 1
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EXPAND_OUTLINE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => $this->params['next_order'] + 2
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::EXPAND_TEXT,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => $this->params['next_order'] + 3
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => $this->params['next_order'] + 4
            ]
        );

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_METADESCRIPTION,
            [
                'process_id' => $this->params['process_id'],
                'meta' => [],
                'order' => $this->params['next_order'] + 5
            ]
        );
    }
}
