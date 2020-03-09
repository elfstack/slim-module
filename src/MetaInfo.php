<?php
namespace ElfStack\SlimModule;

class MetaInfo
{
    protected string $defaultKey;
    protected array $config;
    protected string $from;

    static function create($key, array $config = [])
    {
        $meta = new self($key, $config);
        $meta->from = '(inline MetaInfo::create)';
        return $meta;
    }

    public function __construct($key, $config)
    {
        $this->defaultKey = $key;
        $this->config = $config;
        $this->from = '(inline `MetaInfo` object)';

        // 规范化 config
        $this->config['apis'] = $this->config['apis'] ?? [];
        $this->config['services'] = $this->config['services'] ?? [];
        $this->config['api_prefix'] = $this->config['api_prefix'] ?? '';
        $this->config['namespace'] = rtrim($this->config['namespace'] ?? '', '\\').'\\';
    }

    public function namespace(string $namespace = null)
    {
        if (null === $namespace) {
            return $this->config['namespace'];
        }

        $this->config['namespace'] = rtrim($namespace, '\\').'\\';
        return $this;
    }

    public function apiPrefix(string $prefix = null)
    {
        if (null === $prefix) {
            return $this->config['api_prefix'];
        }

        $this->config['api_prefix'] = $prefix;
        return $this;
    }

    public function service($name, $callback)
    {
        $this->config['services'][$name] = $callback;
        return $this;
    }

    public function get($uri, $callback)
    {
        $this->config['apis']["GET $uri"] = $callback;
        return $this;
    }

    public function post($uri, $callback)
    {
        $this->config['apis']["POST $uri"] = $callback;
        return $this;
    }

    public function put($uri, $callback)
    {
        $this->config['apis']["PUT $uri"] = $callback;
        return $this;
    }

    public function patch($uri, $callback)
    {
        $this->config['apis']["PATCH $uri"] = $callback;
        return $this;
    }

    public function delete($uri, $callback)
    {
        $this->config['apis']["DELETE $uri"] = $callback;
        return $this;
    }

    public function options($uri, $callback)
    {
        $this->config['apis']["OPTIONS $uri"] = $callback;
        return $this;
    }

    public function any($uri, $callback)
    {
        $this->config['apis']["GET,POST,PATCH,PUT,DELETE,OPTIONS $uri"] = $callback;
        return $this;
    }

    public function map(array $methods, $uri, $callback)
    {
        $methodStr = implode(',', $methods);
        $this->config['apis']["$methodStr $uri"] = $callback;
        return $this;
    }

    public function getDefaultKey()
    {
        return $this->defaultKey;
    }

    public function setTraceInfo(string $info)
    {
        $this->from = $info;
        return $this;
    }

    public function getTraceInfo()
    {
        return $this->from;
    }

    public function getNamespacePrefix()
    {
        return $this->config['namespace'];
    }

    public function getApiPrefix()
    {
        return $this->config['api_prefix'];
    }

    public function getRoutes()
    {
        return $this->config['apis'];
    }

    public function getServices()
    {
        return $this->config['services'];
    }
}