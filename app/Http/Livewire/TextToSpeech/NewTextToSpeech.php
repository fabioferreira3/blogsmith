<?php

namespace App\Http\Livewire\TextToSpeech;

use App\Enums\Language;
use App\Jobs\SocialMedia\CreateSocialMediaPost;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewTextToSpeech extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public mixed $tone;
    public mixed $style;
    public bool $linkedin;
    public array $platforms;
    public mixed $more_instructions;
    public bool $modal;
    public $title;

    public function mount()
    {
        $this->title = 'New social media post';
    }

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->tone = null;
        $this->style = null;
        $this->more_instructions = null;
        $this->platforms = [
            'Linkedin' => false,
            'Facebook' => false,
            'Instagram' => false,
            'Twitter' => false,
            'TikTok' => false
        ];
    }

    public function render()
    {
        return view('livewire.text-to-speech.text-to-speech')->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'source_url' => 'required_if:source,youtube,website_url|url',
        'platforms' => 'required|array',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'tone' => 'nullable',
        'style' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your social media post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your social media post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
    ];

    public function process()
    {
        $this->validate();
        CreateSocialMediaPost::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'tone' => $this->tone,
                'style' => $this->style,
                'keyword' => $this->keyword,
                'more_instructions' => $this->more_instructions,
                'platforms' => $this->platforms
            ]
        ]);

        return redirect()->to('/dashboard');
    }
}