<div>
    <x-filament::button
        tag="button"
        type="button"
        x-data
        x-on:click="$wire.mountAction('feedback', { page_url: window.location.pathname, page_title: document.title })"
        icon="heroicon-o-chat-bubble-left-right"
        color="gray"
        outlined
        size="sm"
    >
        <span class="fi-btn-text-responsive">{{ __('Feedback') }}</span>
    </x-filament::button>

    <x-filament-actions::modals />
</div>
