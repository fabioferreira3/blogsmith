<?php

namespace App\Repositories;

use App\Enums\DocumentTaskEnum;
use App\Enums\Tone;
use App\Helpers\PromptHelper;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Str;

class DocumentRepository
{
    protected PromptHelper $promptHelper;
    protected $document;

    public function __construct(Document $document = null)
    {
        $this->document = $document;
        $this->promptHelper = new PromptHelper();
    }

    public function create(array $params): Document
    {
        return Document::create([
            ...$params,
            'meta' => [
                'context' => '',
                'raw_structure' => [],
                'tone' => Tone::CASUAL->value
            ]
        ]);
    }

    public function addHistory(array $payload, array $tokenUsage)
    {
        $content = is_array($payload['content']) ? json_encode($payload['content']) : $payload['content'];
        $this->document->history()->create([
            'description' => $payload['field'],
            'content' => $content,
            'word_count' => Str::wordCount($content),
            'prompt_token_usage' => $tokenUsage['prompt'],
            'completion_token_usage' => $tokenUsage['completion'],
            'total_token_usage' => $tokenUsage['total'],
            'model' => $tokenUsage['model']
        ]);
    }

    public function updateMeta($attribute, $value)
    {
        return $this->document->update(['meta' => array_merge($this->document->meta, [$attribute => $value])]);
    }

    public function publishText()
    {
        $content = str_replace(["\r", "\n"], '', $this->document->normalized_structure);
        $this->document->update([
            'content' => $content,
            'word_count' => Str::wordCount($content)
        ]);
    }

    public function createTask(DocumentTaskEnum $task, array $params)
    {
        DocumentTask::create([
            'name' => $task->value,
            'document_id' => $this->document->id,
            'process_id' => $params['process_id'],
            'job' => $task->getJob(),
            'status' => $params['status'] ?? 'ready',
            'meta' => $params['meta'] ?? [],
            'order' => $params['order'] ?? 1,
        ]);
    }
}