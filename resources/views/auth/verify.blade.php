@extends('layouts.app')

@section('title', env('APP_NAME'))

@section('content')
  <div class="flex-center position-ref full-height">
    <div class="content">
  		<div class="m-b-md">
	      <img
          class="logo"
	      	src="{{ asset('img/logo.png') }}"
	      	title="{{ env('APP_NAME') }}"
          style="width: 200px;" />
	    </div>

      <div class="title m-b-md">
        {{ $message }}
      </div>

      @if($show_login)
      	<a href="{{ $login_url }}" class="btn">Login</a>
      @endif
    </div>
  </div>
@endsection
