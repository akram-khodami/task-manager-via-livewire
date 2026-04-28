<div>
    <div class="folder-name mb-1"
         wire:click="setActiveFolder({{ $folder->id }})"
         style="cursor: pointer; {{ $activeFolder == $folder->id ? 'background-color: #e3f2fd; border-radius: 5px; padding: 5px;' : '' }}">
        <span>📁 {{ $folder->name }}</span>
    </div>

    @if($folder->children && $folder->children->count() > 0)
        <ul style="list-style-type: none; margin-left: 20px;">
            @foreach($folder->children as $child)
                <li>
                    @include('livewire.folder-item', ['folder' => $child, 'activeFolder' => $activeFolder])
                </li>
            @endforeach
        </ul>
    @endif
</div>
