<div id="modal" class="fixed inset-0 w-full h-full z-20 bg-black bg-opacity-30 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-lg w-full sm:w-4/5 md:w-3/5 lg:w-3/5">
      <div class="py-4 text-left px-6">
        <div role='button' wire:click="$emitUp('closeHistoryModal')" class="flex justify-between items-center pb-3">
          <p class="text-2xl font-bold">History - "{{$field}}"</p>
          <div role="button" class="cursor-pointer z-50" id="close">
            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
              <path d="M14.1 4.93l-1.4-1.4L9 6.59 5.3 3.53 3.9 4.93 7.59 8.5 3.9 12.07l1.4 1.43L9 10.41l3.7 3.07 1.4-1.43L10.41 8.5l3.7-3.57z"></path>
            </svg>
          </div>
        </div>

        @if ($history->count())
        <table class="table-fixed border-separate w-full">
            <thead>
                <tr class="bg-zinc-200">
                    <th width="20%" class="text-zinc-700 py-2 px-4 border border-zinc-300 rounded-lg">Date</th>
                    <th width="60%"class="text-zinc-700 py-2 px-4 border border-zinc-300 rounded-lg">Content</th>
                    <th width="20%" class="text-zinc-700 py-2 px-4 border border-zinc-300 rounded-lg text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                    <tr class="group">
                        <td class="text-zinc-700 text-sm py-2 px-4 border border-zinc-300 rounded-lg">{{$item['created_at']}}</td>
                        <td class="text-zinc-700 py-2 px-4 border border-zinc-300 rounded-lg">{{$item['content']}}</td>
                        <td class="text-zinc-700 py-2 px-4 border border-zinc-300 rounded-lg text-center">
                            <button wire:click="apply('{{ $item['content'] }}')" class="group-hover:block hidden bg-zinc-400 text-white text-sm px-4 py-1 rounded-lg m-auto">Apply this one</button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        @else
            No history available
        @endif

        <div class="flex justify-end pt-2">
          <button wire:click="$emitUp('closeHistoryModal')" class="bg-secondary hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
            Close
          </button>
        </div>

      </div>
    </div>
</div>