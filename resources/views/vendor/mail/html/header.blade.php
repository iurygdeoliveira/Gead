@props([
    'url',
    'logo' => null,
    'logoDark' => null,
    'brand' => null,
])
@php
    $configLogo = config('orbit-theme.mail.logo');
    $configLogoDark = config('orbit-theme.mail.logo_dark');
    
    $resolvedLogo = $logo ?? (str_starts_with($configLogo, 'http') ? $configLogo : asset($configLogo));
    $resolvedLogoDark = $logoDark ?? ($configLogoDark ? (str_starts_with($configLogoDark, 'http') ? $configLogoDark : asset($configLogoDark)) : null);
    
    $logoAlt = config('orbit-theme.mail.logo_alt') ?? $brand ?? config('app.name');
    $logoHeight = (int) config('orbit-theme.mail.logo_height', 90);
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($resolvedLogo)
@if ($resolvedLogoDark)
<picture>
<source srcset="{{ $resolvedLogoDark }}" media="(prefers-color-scheme: dark)">
<img src="{{ $resolvedLogo }}" alt="{{ $logoAlt }}" height="{{ $logoHeight }}" class="logo" style="filter: drop-shadow(0 0 12px rgba(250, 255, 229, 0.95)) drop-shadow(0 0 32px rgba(244, 255, 199, 0.75)) drop-shadow(0 0 60px rgba(229, 255, 138, 0.35)) drop-shadow(0 4px 16px rgba(0, 0, 0, 0.65));">
</picture>
@else
<img src="{{ $resolvedLogo }}" alt="{{ $logoAlt }}" height="{{ $logoHeight }}" class="logo" style="filter: drop-shadow(0 0 12px rgba(250, 255, 229, 0.95)) drop-shadow(0 0 32px rgba(244, 255, 199, 0.75)) drop-shadow(0 0 60px rgba(229, 255, 138, 0.35)) drop-shadow(0 4px 16px rgba(0, 0, 0, 0.65));">
@endif
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
