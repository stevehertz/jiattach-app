<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? config('app.name') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
<link href="https://fonts.googleapis.com" rel="preconnect" />
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.min.css">

@livewireStyles

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#205938",
                    "background-light": "#f6f7f8",
                    "background-dark": "#101922",
                },
                fontFamily: {
                    "display": ["Inter", "sans-serif"]
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
            },
        },
    }
</script>
<style>
    body {
        font-family: 'Inter', sans-serif;
    }
</style>
