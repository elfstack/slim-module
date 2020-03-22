<?php
namespace ElfStack\SlimModule\Providers;

use ElfStack\SlimModule\ServiceProvider;
use ElfStack\SlimModule\ModuleManager;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->container['module'] = function () {
            return new ModuleManager($this->app, ['prefix' => 'App\Modules']);
        };
    }
}
