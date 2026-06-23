<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="$brandUrl ?? config('app.url')" :logo="$logo ?? null" :logo-dark="$logoDark ?? null" :brand="$brand ?? null">
{{ $brand ?? config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $brand ?? config('app.name') }}. {{ __('All rights reserved.') }}<br>
Um projeto <a href="https://labsis.dev.br" target="_blank" style="display:inline-block; vertical-align:middle; line-height:1;"><img src="{{ asset('images/labsis_logo.png') }}" alt="LabSIS" style="height: 20px; vertical-align: middle; margin: 0 2px; display: inline-block; border: none;"></a>, precisando de software ?
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
