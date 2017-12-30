@if (config('app.env') === 'production' && config('services.google.analytics'))
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-20037187-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{ config('services.google.analytics') }}');
</script>
@endif
