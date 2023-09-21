<div class="flex flex-col">
    @include('livewire.common.header', [
        'icon' => 'hashtag',
        'label' =>
            $document->status->value == 'draft'
                ? __('social_media.new_social_media_post')
                : __('social_media.social_media_post'),
        'suffix' => $document->title,
    ])

    @if ($generating)
        <div class="flex flex-col mt-8 border-1 border rounded-lg bg-white p-8">
            <div class="flex justify-between items-center">
                @include('livewire.common.label', ['title' => __('social_media.generating')])
            </div>
        </div>
    @endif

    @if (!$generating)
        <div class="flex flex-col mt-8 border-1 border rounded-lg bg-white p-8">
            <div class="flex justify-between items-center cursor-pointer h-full" wire:click="toggleInstructions">
                @include('livewire.common.label', ['title' => __('social_media.instructions')])
                <div>
                    <x-icon :name="$showInstructions ? 'arrow-circle-up' : 'arrow-circle-down'" class="w-8 h-8 text-zinc-500" />
                </div>
            </div>
            @if ($showInstructions)
                <div class="pt-2 border-t mt-4">
                    <div>
                        <div class="flex flex-col md:grid md:grid-cols-2 gap-8 mt-2">
                            {{-- Col 1 --}}
                            <div class="w-full flex flex-col gap-6">
                                <div class="flex flex-col gap-3">
                                    {{-- Platforms --}}
                                    <div>
                                        <div class="flex gap-2 items-center">
                                            <label
                                                class="font-bold text-lg text-zinc-700">{{ __('social_media.target_platforms') }}:</label>
                                            @include('livewire.common.help-item', [
                                                'header' => __('social_media.target_platforms'),
                                                'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms(),
                                            ])
                                        </div>
                                        <div class='grid grid-cols-2 gap-8 mt-2'>
                                            <div class='flex flex-col gap-2'>
                                                <x-checkbox md id="facebook" name="facebook" label="Facebook"
                                                    wire:model.defer="platforms.Facebook" />
                                                <x-checkbox md id="instagram" name="instagram" label="Instagram"
                                                    wire:model.defer="platforms.Instagram" />
                                                <x-checkbox md id="twitter" name="twitter" label="Twitter"
                                                    wire:model.defer="platforms.Twitter" />
                                            </div>
                                            <div class='flex flex-col gap-2'>
                                                <x-checkbox md id="linkedin" name="linkedin" label="Linkedin"
                                                    wire:model.defer="platforms.Linkedin" />
                                            </div>
                                        </div>
                                        @if ($errors->has('platforms'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('platforms') }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex gap-2 items-center">
                                            <label class="font-bold text-lg text-zinc-700">Generate AI Image:</label>
                                            @include('livewire.common.help-item', [
                                                'header' => __('social_media.target_platforms'),
                                                'content' => App\Helpers\InstructionsHelper::socialMediaPlatforms(),
                                            ])
                                        </div>
                                        <div class='grid grid-cols-2 gap-8 mt-2'>
                                            <div class='flex flex-col gap-2'>
                                                <x-checkbox md id="generate_img" name="generate_img" label="Yes"
                                                    wire:model.defer="generateImage" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-6">
                                    {{-- Source --}}
                                    <div>
                                        <div class="flex gap-2 items-center">
                                            <label
                                                class="font-bold text-lg text-zinc-700">{{ __('social_media.source') }}:</label>
                                            @include('livewire.common.help-item', [
                                                'header' => __('social_media.source'),
                                                'content' => App\Helpers\InstructionsHelper::sources(),
                                            ])
                                        </div>
                                        <select name="provider" wire:model="source"
                                            class="p-3 rounded-lg border border-zinc-200 w-full">
                                            @include('livewire.common.source-providers-options')
                                        </select>
                                    </div>

                                    {{-- Context --}}
                                    <div>
                                        @if ($source === 'free_text')
                                            <div>
                                                <div class="flex flex-col gap-1">
                                                    <label
                                                        class="font-bold text-lg text-zinc-700">{{ __('social_media.description') }}:</label>
                                                    <div class="text-sm">
                                                        {{ __('social_media.describe_subject', ['maxChars' => '30000', 'minWords' => '100']) }}
                                                    </div>
                                                    <div class="text-sm">{{ __('social_media.provide_guidelines') }}
                                                    </div>
                                                </div>
                                                <textarea class="border border-zinc-200 rounded-lg w-full mt-3" rows="8" maxlength="30000" wire:model="context"></textarea>
                                            </div>
                                        @endif
                                        @if ($source === 'youtube')
                                            <label class="font-bold text-lg text-zinc-700">Youtube url:</label>
                                            <input name="url" wire:model="sourceUrl"
                                                class="p-3 border border-zinc-200 rounded-lg w-full" />
                                        @endif
                                        @if ($errors->has('source_url'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                                        @endif

                                        @if ($source === 'website_url')
                                            <label class="font-bold text-lg text-zinc-700">URL:</label>
                                            <input name="url" wire:model="source_url"
                                                class="p-3 border border-zinc-200 rounded-lg w-full" />
                                        @endif
                                        @if ($errors->has('source_url'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('source_url') }}</span>
                                        @endif
                                    </div>
                                    @if ($source === 'website_url' || $source === 'youtube')
                                        <div class="flex flex-col gap-3">
                                            <div class="flex flex-col gap-1">
                                                <label
                                                    class="font-bold text-lg text-zinc-700">{{ __('social_media.further_instructions') }}:</label>
                                                <small>{{ __('social_media.provide_guidelines') }}</small>
                                            </div>

                                            <textarea class="border border-zinc-200 rounded-lg" rows="8" maxlength="5000" wire:model="moreInstructions"></textarea>
                                            @if ($errors->has('more_instructions'))
                                                <span
                                                    class="text-red-500 text-sm">{{ $errors->first('more_instructions') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- Col 2 --}}
                            <div class="w-full flex flex-col gap-6">
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <div class="flex gap-3 mb-2 items-center">
                                            <label
                                                class="font-bold text-lg text-zinc-700">{{ __('social_media.keyword') }}:</label>
                                            @include('livewire.common.help-item', [
                                                'header' => __('social_media.keyword'),
                                                'content' => App\Helpers\InstructionsHelper::socialMediaKeyword(),
                                            ])
                                        </div>
                                        <input name="keyword" wire:model="keyword"
                                            class="p-3 w-full rounded-lg border border-zinc-200" />
                                        @if ($errors->has('keyword'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('keyword') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-3">
                                        <div class="flex gap-2 items-center">
                                            <label
                                                class="font-bold text-lg text-zinc-700">{{ __('social_media.language') }}:</label>
                                            @include('livewire.common.help-item', [
                                                'header' => __('social_media.language'),
                                                'content' => App\Helpers\InstructionsHelper::socialMediaLanguages(),
                                            ])
                                        </div>
                                        <select name="language" wire:model="language"
                                            class="p-3 rounded-lg border border-zinc-200">
                                            @foreach ($languages as $option)
                                                <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('language'))
                                            <span class="text-red-500 text-sm">{{ $errors->first('language') }}</span>
                                        @endif
                                    </div>
                                    @if ($errors->has('context'))
                                        <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-3">
                                    <div class="flex gap-2 items-center">
                                        <label
                                            class="font-bold text-lg text-zinc-700">{{ __('social_media.writing_style') }}:</label>
                                        @include('livewire.common.help-item', [
                                            'header' => __('social_media.writing_style'),
                                            'content' => App\Helpers\InstructionsHelper::writingStyles(),
                                        ])
                                    </div>
                                    <select name="style" wire:model="style"
                                        class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                                        @include('livewire.common.styles-options')
                                    </select>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <div class="flex gap-2 items-center">
                                        <label
                                            class="font-bold text-lg text-zinc-700">{{ __('social_media.tone') }}:</label>
                                        @include('livewire.common.help-item', [
                                            'header' => __('social_media.tone'),
                                            'content' => App\Helpers\InstructionsHelper::writingTones(),
                                        ])
                                    </div>
                                    <select name="tone" wire:model="tone"
                                        class="p-3 rounded-lg border border-zinc-200 focus:border focus:border-zinc-400">
                                        @include('livewire.common.tones-options')
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center mt-8">
                            <button wire:click="process" wire:loading.remove
                                class="flex items-center gap-4 bg-secondary text-xl hover:bg-main text-white font-bold px-4 py-2 rounded-lg">
                                <x-icon name="play" class="w-8 h-8" />
                                <span>{{ __('social_media.generate') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
    @if (count($document->children))
        <div class="grid grid-cols-3 mt-6 gap-6">
            @foreach ($document->children as $post)
                @if ($post->meta['platform'] === 'instagram')
                    <div class="flex flex-col">
                        <div class='flex items-center bg-white rounded-t-lg border border-zinc-200 p-4'>
                            <img class="h-12" src="{{ Vite::asset('resources/images/instagram-logo.png') }}">
                        </div>
                        <div class="border-l border-r border-b border-zinc-200 rounded-b-lg overflow-hidden flex-grow">
                            @livewire('social-media-post.platforms.instagram-post', [$post])
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@if ($displayHistory)
    @livewire('common.history-modal', [$document])
@endif
</div>

<script>
    function toggleAccordion(header) {
        var item = header.parentNode;
        var content = item.getElementsByClassName('accordion-content')[0];
        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            // accordion is currently open, so close it
            content.style.maxHeight = '0px';
            content.style.padding = '0';
            content.style.opacity = '0';
            header.classList.remove("active");
        } else {
            // accordion is currently closed, so open it
            content.style.maxHeight = "600px";
            content.style.padding = '1rem';
            content.style.opacity = '1';
            header.classList.add("active");
        }
    }
</script>
