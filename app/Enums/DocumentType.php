<?php

namespace App\Enums;

enum DocumentType: string
{
    case BLOG_POST = 'blog_post';
    case PARAPHRASED_TEXT = 'paraphrased_text';
    case SOCIAL_MEDIA_GROUP = 'social_media_group';
    case SOCIAL_MEDIA_POST = 'social_media_post';
    case TEXT_TO_SPEECH = 'text_to_speech';
    case AUDIO_TRANSCRIPTION = 'audio_transcription';
    case GENERIC = 'generic';

    public function label()
    {
        return match ($this) {
            self::BLOG_POST => __('common.blog_post'),
            self::PARAPHRASED_TEXT => __('common.paraphrased_text'),
            self::AUDIO_TRANSCRIPTION => __('common.audio_transcription'),
            self::TEXT_TO_SPEECH => __('common.text_to_speech'),
            self::SOCIAL_MEDIA_GROUP => __('common.social_media'),
            self::SOCIAL_MEDIA_POST => __('common.social_media_post'),
            self::GENERIC => 'generic'
        };
    }

    public static function routeNames()
    {
        return [
            self::BLOG_POST->value => 'blog-post-view',
            self::PARAPHRASED_TEXT->value => 'paraphrase-view',
            self::AUDIO_TRANSCRIPTION->value => 'transcription-view',
            self::TEXT_TO_SPEECH->value => 'text-to-speech-view',
            self::SOCIAL_MEDIA_GROUP->value => 'social-media-view'
        ];
    }

    public static function getKeyValues(): array
    {
        return collect([
            self::BLOG_POST,
            self::PARAPHRASED_TEXT,
            self::AUDIO_TRANSCRIPTION,
            self::TEXT_TO_SPEECH,
            self::SOCIAL_MEDIA_GROUP
        ])->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
