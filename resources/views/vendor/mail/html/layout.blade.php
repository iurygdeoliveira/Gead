<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ $brand ?? config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<!--[if mso]>
<style type="text/css">
body, table, td, p, a, h1, h2, h3, li, th {
    font-family: 'Segoe UI', Arial, sans-serif !important;
}
</style>
<![endif]-->
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
border-radius: 0 !important;
border-left: 0 !important;
border-right: 0 !important;
}

.footer {
width: 100% !important;
}

.content-cell {
padding: 24px !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
/* Orbit Mail Theme — dark mode overrides. Spliced into layout.blade.php's
   <style> block at publish time so they survive Laravel's CSS inliner
   (which strips theme CSS after inlining the light styles onto elements).
   !important is required because inline styles otherwise win on specificity.
   Honored by Apple Mail, iOS Mail, and Outlook.com web. Gmail and Outlook
   desktop ignore @media queries and apply their own dark transforms. */

@media (prefers-color-scheme: dark) {
    body,
    .wrapper,
    .body {
        background-color: #1a1d1d !important;
        color: #cdd0d0 !important;
    }

    .header a {
        color: #f3f3f3 !important;
    }

    .inner-body {
        background-color: #262828 !important;
        border-color: rgba(255, 255, 255, 0.07) !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25) !important;
    }

    .inner-body a:not(.button),
    a:not(.button) {
        color: #d3ff42 !important;
    }

    h1, h2, h3 {
        color: #f3f3f3 !important;
    }

    p {
        color: #cdd0d0 !important;
    }

    .subcopy {
        border-top-color: rgba(255, 255, 255, 0.07) !important;
    }

    .subcopy p {
        color: #787c7b !important;
    }

    .subcopy a {
        color: #d3ff42 !important;
    }

    .footer p,
    .footer a {
        color: #686d6d !important;
    }

    .table th {
        border-bottom-color: rgba(255, 255, 255, 0.12) !important;
        color: #f3f3f3 !important;
    }

    .table td {
        border-bottom-color: rgba(255, 255, 255, 0.05) !important;
        color: #cdd0d0 !important;
    }

    .panel {
        border-left-color: #d6ff33 !important;
    }

    .panel-content,
    .panel-content p {
        background-color: #2b3600 !important;
        color: #cdd0d0 !important;
    }
}
</style>
{!! $head ?? '' !!}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
