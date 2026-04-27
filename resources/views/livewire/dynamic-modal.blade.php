<div>
    @if ($modalOpen)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header {{ $modalColorClass ?? 'bg-info' }}">
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($formComponent)
                            <livewire:dynamic-component :is="$formComponent" :itemId="$modalData['id'] ?? null" :modalData="$modalData" />
                        @else
                            <p>فرم مشخص نشده است.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
