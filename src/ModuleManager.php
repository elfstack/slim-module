<?php
namespace ElfStack\SlimModule;

use App\Modules\Auth\Meta;

class ModuleManager
{
    protected \Slim\App $app;

    protected $prefix = '';
    protected $registered = [];
    protected $services = [];
    protected $strictCall = true;

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

    /**
     * 解析输入参数到 MetaInfo
     *
     * @param mixed $module
     * @return MetaInfo
     */
    protected function resolveModule($module)
    {
        if ($module instanceof MetaInfo) {
            return $module;
        }
        // FIXME: assumed string
        $from = $module;
        $tries = [$this->prefix.$module.'\Meta', $module.'\Meta', $module];
        foreach ($tries as $try) {
            if (class_exists($try)) {
                // FIXME: check interface
                $module = $try::info();
                $module->setTraceInfo($from);
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
            $this->app->map(explode(',', $route[0]), $api_prefix.$route[1], $this->resolveHandler($module, $handler));
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

    public function setStrictCall(bool $strict = true)
    {
        $this->strictCall = $strict;
        return $this;
    }

    public function getStrictCall()
    {
        return $this->strictCall;
    }

    public function noExceptionCall($service, ...$args)
    {
        $origin = $this->getStrictCall();
        $result = $this->setStrictCall(false)->call($service, ...$args);
        $this->setStrictCall($origin);
        return $result;
    }

    public function call($service, ...$args)
    {
        [$name, $foo] = explode('.', $service, 2);
        if ($name == '*') {
            $result = [];
            foreach (array_keys($this->registered) as $module) {
                $result[] = $this->call("$module.$foo", ...$args);
            }
            return $result;
        }


        if (!isset($this->services[$service])) {
            if ($this->getStrictCall())
                throw new \RuntimeException("Unable to call service `$service`, service not registered.");
            else
                return null;
        }
        return $this->services[$service](...$args);
    }

    public function collect()
    {
        $result = '';
        foreach ($this->registered as $key => $module) {
            $result .= "$key: ".$module->getTraceInfo()."\n\troutes:\n";
            foreach ($module->getRoutes() as $route => $handler) {
                $result .= "\t\t$route\n";
            }
            $result .= "\tservices:\n";
            foreach ($module->getServices() as $service => $handler) {
                $result .= "\t\t$key.$service\n";
            }
        }
        return $result;
    }
}
