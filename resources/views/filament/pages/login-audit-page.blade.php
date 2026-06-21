<x-filament-panels::page>
    @if ($selectedUserId)
        <div class="flex items-center justify-between mb-4">
            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-left"
                wire:click="selectUser(null)"
            >
                Voltar para Lista
            </x-filament::button>

            @php
                $user = \App\Models\User::find($selectedUserId);
            @endphp
            
            <div class="flex items-center gap-2">
                 <x-filament::avatar
                    :src="filament()->getUserAvatarUrl($user)"
                    :alt="$user->name"
                    size="md"
                 />
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
            </div>
        </div>

        {{ $this->table }}
    @else
        <div class="mb-6 max-w-md">
            <x-filament::input.wrapper
                inner-prefix-icon="heroicon-m-magnifying-glass"
            >
                <x-filament::input
                    type="search"
                    placeholder="Pesquisar por nome ou e-mail..."
                    wire:model.live.debounce.500ms="search"
                />
            </x-filament::input.wrapper>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
            @forelse ($this->getUsersWithAudits() as $user)
                <x-filament::section
                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition duration-200"
                    style="width: 100%; max-width: 320px; flex: 0 0 auto;"
                    wire:click="selectUser({{ $user->id }})"
                >
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 1rem; text-align: left; width: 100%; overflow: hidden;">
                        <x-filament::avatar
                            :src="filament()->getUserAvatarUrl($user)"
                            :alt="$user->name"
                            size="lg"
                        />
                        <div style="flex: 1; overflow: hidden;">
                            <h3 style="font-weight: 600; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.25rem;">
                                {{ $user->name }}
                            </h3>
                            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </x-filament::section>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-white/10">
                    <x-filament::icon
                        icon="heroicon-m-magnifying-glass"
                        class="mx-auto h-12 w-12 text-gray-400"
                    />
                    <p class="mt-4 text-gray-500">Nenhum usuário encontrado para "{{ $search }}".</p>
                </div>
            @endforelse
        </div>
    @endif
</x-filament-panels::page>
