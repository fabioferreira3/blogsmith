<div class="flex flex-col gap-6">
    @include('livewire.common.header', ['icon' => 'sort-ascending', 'label' => __('summarizer.new_summary')])
    <div class="flex flex-col gap-6 p-4 border rounded-lg">
        <div class="w-full flex flex-col md:grid md:grid-cols-3 gap-6">
            <!-- Source -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.source')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('blog.source'),
                    'content' => App\Helpers\InstructionsHelper::sources()
                    ])
                </div>
                <select name="provider" wire:model="source" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.source-providers-options')
                </select>
            </div>
            <!-- END: Source -->

            <!-- Source Language -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.source_language')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.source_language'),
                    'content' => App\Helpers\InstructionsHelper::summarizerLanguages()
                    ])
                </div>
                <select name="language" wire:model="sourceLanguage" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.languages-options')
                </select>
                @if($errors->has('sourceLanguage'))
                <span class="text-red-500 text-sm">{{ $errors->first('sourceLanguage') }}</span>
                @endif
            </div>
            <!-- END: Source Language -->

            <!-- Target Language -->
            <div class="flex flex-col gap-3">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.target_language')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.target_language'),
                    'content' => App\Helpers\InstructionsHelper::summarizerLanguages()
                    ])
                </div>
                <select name="language" wire:model="targetLanguage" class="p-3 rounded-lg border border-zinc-200">
                    @include('livewire.common.languages-options')
                </select>
                @if($errors->has('targetLanguage'))
                <span class="text-red-500 text-sm">{{ $errors->first('targetLanguage') }}</span>
                @endif
            </div>
            <!-- END: Target Language -->
        </div>
        <div class="w-full flex flex-col md:grid md:grid-cols-3 md:items-center gap-6">
            <!-- File input -->
            @if (in_array($source, ['docx', 'pdf_file', 'csv', 'json']))
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700">{{ __('blog.file_option') }}</label>
                <input type="file" name="fileInput" wire:model="fileInput"
                    class="p-3 border border-zinc-200 rounded-lg w-full" />
                @if ($errors->has('fileInput'))
                <span class="text-red-500 text-sm">{{ $errors->first('fileInput') }}</span>
                @endif
            </div>
            @endif
            <!-- END: File input -->

            <!-- Free Text -->
            @if ($source === 'free_text')
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700 flex items-center">
                    Text:
                </label>
                <textarea class="border border-zinc-200 rounded-lg" rows="5" maxlength="30000"
                    wire:model="context"></textarea>
                @if($errors->has('context'))
                <span class="text-red-500 text-sm">{{ $errors->first('context') }}</span>
                @endif
            </div>
            @endif
            <!-- END: Free Text -->

            <!-- Source URLs -->
            @if ($source === 'website_url' || $source === 'youtube')
            <div class="flex flex-col gap-3 col-span-2">
                <label class="font-bold text-xl text-zinc-700 flex items-center">
                    URL
                </label>
                <input type="text" name="sourceUrl" wire:model="sourceUrl"
                    class="p-3 border border-zinc-200 rounded-lg w-full" />

                @if ($errors->has('sourceUrl'))
                <span class="text-red-500 text-sm">{{ $errors->first('sourceUrl') }}</span>
                @endif
            </div>
            @endif
            <!-- END: Source URLs -->

            <!-- Word count -->
            <div class="flex flex-col gap-3 col-span-1">
                <div class="flex gap-2 items-center">
                    <label class="text-xl font-bold text-gray-700">{{__('summarizer.word_count')}}:</label>
                    @include('livewire.common.help-item', [
                    'header' => __('summarizer.word_count'),
                    'content' => App\Helpers\InstructionsHelper::wordsCount()
                    ])
                </div>
                <input type="number" min="100" max="3000" name="max_words_count" wire:model.lazy="maxWordsCount"
                    class="p-3 rounded-lg border border-zinc-200 w-2/3" />
                @if($errors->has('maxWordsCount'))
                <span class="text-red-500 text-sm">{{ $errors->first('maxWordsCount') }}</span>
                @endif
            </div>
            <!-- END: Word count -->
        </div>

        <!-- Generate button -->
        <div class="flex justify-start mt-4">
            <button wire:click="process" wire:loading.remove
                class="bg-secondary text-xl text-white font-bold px-4 py-2 rounded-lg">
                {{__('summarizer.generate')}}!
            </button>

            <div wire:loading wire:target="process">
                <div class="flex items-center gap-2 bg-secondary text-xl text-white font-bold px-4 py-2 rounded-lg">
                    <x-loader color="white" />
                    <span>{{__('summarizer.preparing')}}</span>
                </div>
            </div>
        </div>
        <!-- END: Generate button -->
    </div>
</div>