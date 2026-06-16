<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white shadow-sm ring-1 ring-gray-950/5 rounded-xl dark:bg-gray-900 dark:ring-white/10">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <h2 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                        {{ __('Informações do Feedback') }}
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        {{ __('Verifique os detalhes abaixo antes de confirmar a exclusão.') }}
                    </p>

                    <dl class="mt-6 space-y-6 divide-y divide-gray-100 border-t border-gray-200 text-sm leading-6 dark:divide-gray-800 dark:border-gray-800">
                        <div class="pt-6 sm:flex">
                            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6 dark:text-white">
                                {{ __('Usuário') }}
                            </dt>
                            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
                                <div class="text-gray-900 dark:text-white">{{ $record->user->name ?? '—' }}</div>
                            </dd>
                        </div>

                        <div class="pt-6 sm:flex">
                            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6 dark:text-white">
                                {{ __('Página') }}
                            </dt>
                            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
                                <div class="text-gray-900 dark:text-white">{{ $record->page_title ?? '—' }}</div>
                            </dd>
                        </div>

                        <div class="pt-6 sm:flex">
                            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6 dark:text-white">
                                {{ __('URL') }}
                            </dt>
                            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
                                <div class="text-gray-900 dark:text-white break-all">{{ $record->page_url ?? '—' }}</div>
                            </dd>
                        </div>

                        <div class="pt-6 sm:flex">
                            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6 dark:text-white">
                                {{ __('Mensagem') }}
                            </dt>
                            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
                                <div class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $record->message }}</div>
                            </dd>
                        </div>

                        <div class="pt-6 sm:flex">
                            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6 dark:text-white">
                                {{ __('Data de envio') }}
                            </dt>
                            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
                                <div class="text-gray-900 dark:text-white">{{ $record->created_at->format('d/m/Y H:i') }}</div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
