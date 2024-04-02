<?php

namespace Azit\Ddd;

use Illuminate\Support\ServiceProvider;

class AzitProvider extends ServiceProvider {

    public function register(){
        $this -> mergeConfigFrom(__DIR__.'/../config/library.php', 'library');
    }

    public function boot(){
        $this->publishes([
            __DIR__.'/../config/library.php' => config_path('library.php'),
        ], 'config');
    }

}