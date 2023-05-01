@extends('frontend.layouts.app')
@section('title', 'Notification Detail')
@section('content')
<div class="notification_detail">
    <div class="card my-card mb-2">
        <div class="card-body py-2 text-center">
            <div>
                <img src="{{ asset('img/notification.png') }}" width="220px" alt="">
            </div>
            <h6>{{ $notification->data['title'] }}</h6>
            <p class="mb-1">{{ $notification->data['message'] }}</p>
            <p class="text-secondary mb-3"><small>{{ $notification->created_at->diffForHumans() }}</small></p>
            <a href="{{ url($notification->data['web_link']) }}" class="btn btn-theme">Continue</a>
        </div>
    </div>
</div>
@endsection
