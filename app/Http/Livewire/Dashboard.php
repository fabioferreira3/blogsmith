<?php

namespace App\Http\Livewire;

use App\Jobs\ProcessRequestFromUrl;
use App\Repositories\TextRequestRepository;
use Livewire\Component;


class Dashboard extends Component
{
    public string $source_url;
    public string $language;
    public string $keyword;
    public string $tone;

    public function __construct()
    {
        $this->source_url = '';
        $this->language = 'en';
        $this->keyword = '';
        $this->tone = '';
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function newPost()
    {
        return redirect()->to('/new');
    }

    public function process()
    {
        $textRequest = new TextRequestRepository();

        $textRequest = $textRequest->create([
            'source_url' => $this->source_url,
            'source_provider' => 'youtube',
            'language' => $this->language,
            'keyword' => $this->keyword,
            'tone' => $this->tone
        ]);

        ProcessRequestFromUrl::dispatchIf($textRequest->source_url, $textRequest);
    }
}
