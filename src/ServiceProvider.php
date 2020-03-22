<?php
namespace ElfStack\SlimModule;

use Slim\App;

abstract class ServiceProvider
{
    protected $app;
    protected $container;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();

        $this->boot();
    }

    protected function boot()
    {

    }

    /**
     * Register new service
     *
     * @param \Slim\App $app
     * @return void
     */
    public function register()
    {
        // register app
    }
}
