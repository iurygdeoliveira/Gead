<x-filament-widgets::widget>
    @php
        $data = $this->getData();
    @endphp

    @if(!empty($data))
        <x-filament::section :heading="'Status das Avaliações - ' . $data['name']" collapsible>
            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-medium" style="display: flex; justify-content: space-between;">
                        <span class="text-gray-700 dark:text-gray-200">Total de Avaliações: {{ $data['total'] }}</span>
                        <div class="flex gap-3 text-xs text-gray-500 dark:text-gray-400" style="display: flex; gap: 0.75rem;">
                            <span style="color: var(--primary-600, #ccff03);">
                                {{ $data['realizadas'] }} Realizadas ({{ number_format($data['realizadas_pct'], 1) }}%)
                            </span>
                            <span>•</span>
                            <span style="color: var(--danger-600, #dc2626);">
                                {{ $data['nao_realizadas'] }} Não Realizadas ({{ number_format($data['nao_realizadas_pct'], 1) }}%)
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex h-6 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700 text-[10px] font-bold text-white leading-none" 
                         style="display: flex; width: 100%; height: 1.5rem; overflow: hidden; border-radius: 9999px; background-color: #374151; color: white; font-size: 10px; font-weight: bold; line-height: 1;">
                        
                        @if($data['realizadas'] > 0)
                            <div 
                                class="flex items-center justify-center transition-all hover:opacity-90" 
                                style="display: flex; align-items: center; justify-content: center; width: {{ $data['realizadas_pct'] }}%; background-color: var(--primary-500, #ccff03); color: #000;"
                                title="Realizadas: {{ $data['realizadas'] }} ({{ number_format($data['realizadas_pct'], 1) }}%)"
                            >
                                @if($data['realizadas_pct'] >= 10)
                                    {{ number_format($data['realizadas_pct'], 0) }}%
                                @endif
                            </div>
                        @endif

                        @if($data['nao_realizadas'] > 0)
                            <div 
                                class="flex items-center justify-center transition-all hover:opacity-90" 
                                style="display: flex; align-items: center; justify-content: center; width: {{ $data['nao_realizadas_pct'] }}%; background-color: var(--danger-500, #ef4444);"
                                title="Não Realizadas: {{ $data['nao_realizadas'] }} ({{ number_format($data['nao_realizadas_pct'], 1) }}%)"
                            >
                                @if($data['nao_realizadas_pct'] >= 10)
                                    {{ number_format($data['nao_realizadas_pct'], 0) }}%
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
