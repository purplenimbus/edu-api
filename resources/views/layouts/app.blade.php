<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->

    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet"/>
    <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
    <link href="https://unpkg.com/basscss-colors@2.2.0/css/colors.css" rel="stylesheet">
    <link href="https://unpkg.com/basscss-background-colors@2.1.0/css/background-colors.css" rel="stylesheet">
    <link href="https://unpkg.com/basscss-responsive-layout@1.0.1/css/responsive-layout.css" rel="stylesheet">

  </head>
  <body>
    <section class="full-height flex items-center">
      <div class="md-col-3 mx-auto">
        <div class="flex flex-column bg-white rounded">
          @yield('content')
        </div>
      </div>
    </section>
  </body>
</html>
