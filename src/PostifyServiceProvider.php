<?php

namespace Melsaka\Postify;

use Illuminate\Support\ServiceProvider;

class PostifyServiceProvider extends ServiceProvider
{
    // package migrations
    private $migration = __DIR__ . '/database/migrations/';

    private $config = __DIR__ . '/config/postify.php';


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->config, 'postify');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom([ $this->migration ]);

        $this->publishes([ $this->config => config_path('postify.php') ], 'postify');
    }
}
