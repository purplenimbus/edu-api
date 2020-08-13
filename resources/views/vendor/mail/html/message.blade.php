@component('mail::layout')
	{{-- Header --}}
	@slot('header')
		@component('mail::header', ['url' => config('app.url')])
			{{ config('app.name') }}
		@endcomponent
	@endslot

	{{-- Body --}}
	@component('mail::panel')
		{{ $slot }}
	@endcomponent

	{{-- Subcopy --}}
	@isset($subcopy)
		@slot('subcopy')
			@component('mail::subcopy')
				{{ $subcopy }}
			@endcomponent
		@endslot
	@endisset

	{{-- Footer --}}
	@slot('footer')
		@component('mail::footer')
			@component('mail::panel')
				<h3>@lang('email.question')</h3>

				@lang('email.email', ['email' => config('app.email')])
			@endcomponent
			© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.') <br>

			@lang('email.privacy')
		@endcomponent
	@endslot
@endcomponent
