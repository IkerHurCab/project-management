<!DOCTYPE html>
<html class="h-full">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=archivo:500,600,700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/storage/images/faviconJPG.png" />


    
    
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @inertiaHead
  </head>
  <body class="bg-black dark:text-gray-700 h-full text-white dark:bg-gray-100">
    @inertia
  </body>
</html>