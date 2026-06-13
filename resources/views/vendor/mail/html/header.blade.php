@props([
    'url',
    'logo' => null,
    'logoDark' => null,
    'brand' => null,
])
@php
    $resolvedLogo = $logo ?? config('orbit-theme.mail.logo');
    $resolvedLogoDark = $logoDark ?? config('orbit-theme.mail.logo_dark');
    $logoAlt = config('orbit-theme.mail.logo_alt') ?? $brand ?? config('app.name');
    $logoHeight = (int) config('orbit-theme.mail.logo_height', 32);
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($resolvedLogo)
@if ($resolvedLogoDark)
<picture>
<source srcset="{{ $resolvedLogoDark }}" media="(prefers-color-scheme: dark)">
<img src="{{ $resolvedLogo }}" alt="{{ $logoAlt }}" height="{{ $logoHeight }}" class="logo">
</picture>
@else
<img src="{{ $resolvedLogo }}" alt="{{ $logoAlt }}" height="{{ $logoHeight }}" class="logo">
@endif
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
