<?php
namespace ElfStack\SlimModule;

use Slim\App;

class ServiceManager
{
    protected App $app;

    protected $prefix = '';
    protected $services = [];
    
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function register(array $services)
    {
        foreach ($services as $key => $service) {
            $service = $this->registerService($service);
        }
    }

    protected function registerService($service)
    {
        if ($service instanceof ServiceProvider) {
            return $service;
        }

        if (class_exists($service)) {
            (new $service($this->app))->register();
        }
    }
}
