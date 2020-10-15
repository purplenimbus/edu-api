@extends('layouts.app')

@section('title', env('APP_NAME'))

@section('content')
  <div class="p3 center">
    <img
      src="https://res.cloudinary.com/dy0pq7aag/image/upload/v1602730732/nimbus%20learning/undraw_opened_gi4n_xgjs6c.svg"
      width="200px"/>
  </div>
  <div class="p3 center">
    <h1>{{ $title }}</h2>
    @if (isset($message))
    <p>
      {{ $message }}
    </p>
    @endif
    @if (isset($action))
    <button href="#" class="btn btn-primary mt3">Resend Verification</button>
    @endif
  </div>
@endsection