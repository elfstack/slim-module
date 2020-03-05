<?php
namespace ElfStack\SlimModule;

class Manager
{
    protected \Slim\App $app;

    protected $prefix = '';
    protected $registered = [];
    protected $services = [];

    public function __construct(\Slim\App $app, array $config)
    {
        $this->app = $app;
        $this->prefix = rtrim($config['prefix'], '\\').'\\';
    }

    public function register(array $modules)
    {
        foreach ($modules as $key => $module) {
            $module = $this->resolveModule($module);

            if (is_integer($key)) {
                $key = $module->getDefaultKey();;
            }
            $this->registerModule($key, $module);
        }
    }

    protected function resolveModule($module)
    {
        if ($module instanceof MetaInfo) {
            return $module;
        }
        $tries = [$this->prefix.$module.'\Meta', $module.'\Meta', $module];
        foreach ($tries as $try) {
            if (class_exists($try)) {
                // FIXME: check interface
                $module = $try::info();
                break;
            }
        }
        if (false == ($module instanceof MetaInfo)) {
            throw new \Exception("Module `$module` not found.");
        }
        return $module;
    }

    public function isRegistered($key)
    {
        return isset($this->registered[$key]);
    }

    protected function registerModule($key, MetaInfo $module)
    {
        if ($this->isRegistered($key)) {
            throw new \Exception("Register module failed! Conflict key `$key`.");
        }
        $this->registerModuleRoutes($key, $module);
        $this->registerModuleServices($key, $module);
        $this->registered[$key] = $module;
    }

    protected function registerModuleRoutes($key, MetaInfo $module)
    {
        $api_prefix = $module->getApiPrefix();
        foreach ($module->getRoutes() as $route => $handler) {
            $route = explode(' ', $route, 2);
            // TODO validate $route
            // TODO supports multiple routes on single handler
            $this->app->map([$route[0]], $api_prefix.$route[1], $this->resolveHandler($module, $handler));
        }
    }

    protected function registerModuleServices($key, MetaInfo $module)
    {
        $services = $module->getServices();
        foreach ($services as $service => $handler) {
            $this->services["$key.$service"] = $this->resolveHandler($module, $handler);
        }
    }

    protected function resolveHandler(MetaInfo $module, $handler)
    {
        $resolver = $this->app->getContainer()->get('callableResolver');
        try {
            return $resolver->resolve($module->getNamespacePrefix().$handler);
        } catch (\Exception $e) {
            return $resolver->resolve($handler);
        }
    }

    public function call($service, ...$args)
    {
        return $this->services[$service](...$args);
    }

    public function debug()
    {
        unset($this->app);
        return $this;
    }
}