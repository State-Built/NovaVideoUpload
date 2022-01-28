<?php

namespace State\VideoUpload;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use TusPhp\Config as TusConfig;
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
        Nova::serving(function (ServingNova $event) {
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
        TusConfig::set([
            'file' => [
                'dir'  => storage_path('tus_cache/'),
                'name' => 'tusphp.cache',
            ],
        ]);

        $this->app->singleton('tus-server', function ($app) {
            $server = new TusServer();

            $server->setApiPath('/nova-tus')
                   ->setUploadDir(\Storage::path('tmp/videos'));

            return $server;
        });

        Route::any('/nova-tus/{any?}', fn() => $this->app->make('tus-server')->serve())
             ->where('any', '.*')->name('nova.tus');
    }

}
