@extends('frontend.layouts.app')
@section('title', 'Notification')
@section('content')
<div class="notification">
    <h6 class="fw-bold">Notifications</h6>
    <div class="infinite-scroll">
        @foreach ($notifications as $notification)
            <a href="{{ url('/notification', $notification->id) }}">
                <div class="card my-card mb-2">
                    <div class="card-body py-2">
                        <h6><i class="fas fa-bell me-1 {{ is_null($notification->read_at) ? 'text-danger' : null }}"></i> {{ Str::limit($notification->data['title'], 40) }}</h6>
                        <p class="mb-1">{{ Str::limit($notification->data['message'], 100) }}</p>
                        <p class="mb-1 text-secondary"><small>{{ Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i:s A') }}</small></p>
                    </div>
                </div>
            </a>
        @endforeach

        <div class="mt-3">
        {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            // loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            loadingHtml: '<div class="text-center">Loading.....</div>',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });

    });
</script>
@endsection