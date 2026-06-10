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

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($this->getUsersWithAudits() as $user)
                <x-filament::section
                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition duration-200"
                    wire:click="selectUser({{ $user->id }})"
                >
                    <div class="flex flex-col items-center gap-4 text-center w-full overflow-hidden">
                        <x-filament::avatar
                            :src="filament()->getUserAvatarUrl($user)"
                            :alt="$user->name"
                            size="xl"
                        />
                        <div class="w-full">
                            <h3 class="font-semibold text-lg leading-tight wrap-break-word line-clamp-2 px-2">
                                {{ $user->name }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 break-all px-2 overflow-hidden text-ellipsis">
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
