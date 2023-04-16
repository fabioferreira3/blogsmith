<?php

namespace App\Http\Livewire;

use Livewire\Component;


class Templates extends Component
{

    public function __construct()
    {
    }

    public function render()
    {
        return view('livewire.templates');
    }

    public function newBlogPost()
    {
        return redirect()->to('/blog/new');
    }
}