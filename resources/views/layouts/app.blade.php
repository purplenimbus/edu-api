<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
      html, body {
          background-color: #2b3e50;
          color: white;
          font-family: 'Lato', sans-serif;
          font-weight: 200;
          height: 100vh;
          margin: 0;
      }

      .full-height {
          height: 100vh;
      }

      .flex-center {
          align-items: center;
          display: flex;
          justify-content: center;
      }

      .position-ref {
          position: relative;
      }

      .top-right {
          position: absolute;
          right: 10px;
          top: 18px;
      }

      .content {
          text-align: center;
      }

      .title {
        font-size: 2em;
        margin: 0.67em 0;
      }

      .links > a {
          color: #636b6f;
          padding: 0 25px;
          font-size: 13px;
          font-weight: 600;
          letter-spacing: .1rem;
          text-decoration: none;
          text-transform: uppercase;
      }

      .m-b-md {
          margin-bottom: 30px;
      }

      .btn {
        color: #ffffff;
        background-color: #ff7f72;
        border-color: transparent;
        font-weight: 500;
        cursor: default;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        line-height: 1.25;
        border-radius: 100em !important;
        text-transform: uppercase;
        text-decoration: none;
      }
    </style>
  </head>
  <body>
    @yield('content')
  </body>
</html>
