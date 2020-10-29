<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Laravel money
	|--------------------------------------------------------------------------
	*/
	'locale' => config('app.locale', 'en_US'),
	'defaultCurrency' => config('app.currency', 'NGN'),
	'currencies' => [
		'iso' => 'all',
		'bitcoin' => 'all',
		'custom' => []
	]
];
