<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.js')
    {{-- fix FOUC --}}
    <style>
      html {
        visibility: hidden;
        opacity: 0;
      }
    </style>
    @inertiaHead
    @routes
  </head>

  <body>
    @inertia
  </body>

</html>