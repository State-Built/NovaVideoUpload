<?php

namespace Twotp\VideoUpload;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Route;
use TusPhp\Tus\Server as TusServer;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function(ServingNova $event) {
            Nova::script('video-upload', __DIR__ . '/../dist/js/field.js');
            Nova::style('video-upload', __DIR__ . '/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTusServer();
    }

    public function registerTusServer()
    {
        $this->app->singleton('tus-server', function($app) {
            $server = new TusServer('redis');

            $server->setApiPath('/nova-tus')
                   ->setUploadDir(\Storage::path('tmp/videos'));

            return $server;
        });

        Route::any('/nova-tus/{any?}', fn() => $this->app->make('tus-server')->serve())
             ->where('any', '.*');
    }

}
