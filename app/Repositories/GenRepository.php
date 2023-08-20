<?php

namespace App\Repositories;

use App\Enums\DocumentTaskEnum;
use App\Packages\ChatGPT\ChatGPT;
use App\Enums\LanguageModels;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Str;

class GenRepository
{
    public static function generateTitle(Document $document, $context)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(LanguageModels::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeTitle($context, [
                'tone' => $document['meta']['tone'] ?? null,
                'keyword' => $document['meta']['keyword'] ?? null
            ])
        ]]);
        $document->update(['title' => Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"')]);
        $repo->addHistory(
            [
                'field' => 'title',
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function generateMetaDescription(Document $document)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(LanguageModels::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeMetaDescription(
                $document->normalized_structure,
                [
                    'tone' => $document['meta']['tone'] ?? null,
                    'keyword' => $document['meta']['keyword']
                ]
            )
        ]]);
        $repo->updateMeta('meta_description', Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"'));
        $repo->addHistory(
            [
                'field' => 'meta_description',
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function generateSocialMediaPost(Document $document, string $platform)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT();
        $response = $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $promptHelper->writeSocialMediaPost($document->context, [
                    'keyword' => $document->meta['keyword'] ?? null,
                    'platform' => $platform,
                    'tone' => $document->meta['tone'] ?? null,
                    'style' => $document->meta['style'] ?? null,
                    'more_instructions' => $document->meta['more_instructions'] ?? null
                ])
            ]
        ]);
        $repo->updateMeta($platform, $response['content']);
        $repo->addHistory(
            [
                'field' => $platform,
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function paraphraseDocument(Document $document)
    {
        $document->refresh();
        $repo = new DocumentRepository($document);
        $processId = Str::uuid();

        foreach ($document->meta['original_sentences'] as $sentence) {
            $repo->createTask(DocumentTaskEnum::PARAPHRASE_TEXT, [
                'order' => 1,
                'process_id' => $processId,
                'meta' => [
                    'text' => $sentence['text'],
                    'sentence_order' => $sentence['sentence_order']
                ]
            ]);
        }

        $repo->createTask(DocumentTaskEnum::REGISTER_FINISHED_PROCESS, [
            'order' => 99,
            'process_id' => $processId,
            'meta' => [
                'silently' => true
            ]
        ]);

        DispatchDocumentTasks::dispatch($document);

        return $processId;
    }

    public static function paraphraseText(Document $document, array $params)
    {
        $processId = $params['process_id'] ?? Str::uuid();
        $repo = new DocumentRepository($document);
        $order = $params['order'] ?? 1;
        $repo->createTask(DocumentTaskEnum::PARAPHRASE_TEXT, [
            'order' => $params['order'] ?? 1,
            'process_id' => $processId,
            'meta' => [
                'text' => $params['text'],
                'sentence_order' => $params['sentence_order'],
                'tone' => $params['tone'] ?? null
            ]
        ]);
        $repo->createTask(DocumentTaskEnum::REGISTER_FINISHED_PROCESS, [
            'order' => $order + 1,
            'process_id' => $processId,
            'meta' => [
                'silently' => true
            ]
        ]);

        DispatchDocumentTasks::dispatch($document);

        return $processId;
    }

    public static function textToSpeech($document, array $params = [])
    {
        $repo = new DocumentRepository($document);
        $repo->createTask(DocumentTaskEnum::TEXT_TO_SPEECH, [
            'order' => 1,
            'process_id' => $params['process_id'] ?? Str::uuid(),
            'meta' => [
                'text' => $params['text'],
                'voice' => $params['voice'],
                'iso_language' => $params['iso_language']
            ]
        ]);

        DispatchDocumentTasks::dispatch($document);
    }
}
