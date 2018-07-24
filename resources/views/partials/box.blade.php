@if (session('message') && session('message_type'))
    <div class="box box-{{ session('message_type') }}">
        <p>{!! session('message') !!}</p>
    </div>
@endif
