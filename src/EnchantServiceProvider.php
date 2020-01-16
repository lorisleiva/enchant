<?php

namespace Lorisleiva\Enchant;

use Illuminate\Support\ServiceProvider;

class EnchantServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/enchant.php' => config_path('enchant.php')]);

        if ($this->app->runningInConsole()) {
            $this->commands([EnchantCommand::class]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enchant.php', 'enchant');
    }
}
