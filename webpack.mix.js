const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.js('resources/js/app.js','public/js/app.js')
   .scripts(['resources/asset/js/moment.js',
      'resources/asset/js/tooltip.min.js',
      'resources/asset/js/perfect-scrollbar.js',
      'resources/asset/js/bootstrap-notify.min.js',
      'resources/asset/js/sweetalert2.js',
      'resources/asset/js/bootstrap-select.js',
      'resources/asset/js/datatables.bundle.min.js'],'public/js/all.js')
   .styles([
      'resources/asset/css/animate.css',
      'resources/asset/css/socicon.css',
      'resources/asset/css/line-awesome.css',
      'resources/asset/css/flaticon.css',
      'resources/asset/css/fontawesome.css',
      'resources/asset/css/bootstrap-select.css',
      'resources/asset/css/datatables.bundle.min.css',
      'resources/asset/css/style.css'
   ], 'public/css/app.css')
   .copy('resources/asset/js/jquery.printarea.js', 'public/js/jquery.printarea.js')
   .copy('resources/asset/js/bootstrap-datepicker.min.js', 'public/js/bootstrap-datepicker.min.js')
   .copy('resources/asset/js/jquery-ui.js', 'public/js/jquery-ui.js')
   .copy('resources/asset/js/dashboard.js', 'public/js/dashboard.js')
   .copy('resources/asset/js/scripts.bundle.js', 'public/js/scripts.bundle.js')
   .copy('resources/asset/css/print.css', 'public/css/print.css')
   .copy('resources/asset/css/bootstrap-datepicker.min.css', 'public/css/bootstrap-datepicker.min.css')
   .copy('resources/asset/css/jquery-ui.css', 'public/css/jquery-ui.css')
   .copyDirectory('resources/asset/fonts', 'public/fonts');
