<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/js/app.js')
    {{-- fix FOUC --}}
    <style>
      html {
        visibility: hidden;
        opacity: 0;
      }
    </style>
    @inertiaHead
  </head>

  <body>
    @inertia
  </body>

</html>