@extends('layouts.app')

@section('title', env('APP_NAME'))

@section('content')
  <div class="p3 center">
    <img
      src="https://res.cloudinary.com/dy0pq7aag/image/upload/v1602730728/nimbus%20learning/undraw_Mail_sent_re_0ofv_t6gzcl.svg"
      width="200px"/>
  </div>
  <div class="p3 center">
    <h1>{{ __('registration.verification_success') }}</h2>
    <p>
      {!! $message !!}
    </p>
    <a href="{{ $login_url }}" class="btn btn-primary mt3">Login</a>
  </div>
@endsection
