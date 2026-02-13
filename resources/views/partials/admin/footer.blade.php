@php 
// Get site config
$config = site_config();
@endphp 

<footer class="main-footer">
    {!! $siteConfig['copyright'] ?? 'Copyright © ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' !!}
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
    </div>
</footer>