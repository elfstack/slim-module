<?php
namespace ElfStack\SlimModule;

class MetaInfo
{
    protected string $defaultKey;
    protected array $config;
    protected string $from;

    protected string $group_prefix = '';

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

    public function group(string $prefix, callable $fun): void
    {
        $last = $this->group_prefix;
        $this->group_prefix = $prefix;

        $fun($this);

        $this->group_prefix = $last;
    }

    public function service($name, $callback)
    {
        $this->config['services'][$name] = $callback;
        return $this;
    }

    public function get(string $uri, $callback)
    {
        return $this->map(['GET'], $uri, $callback);
    }

    public function post(string $uri, $callback)
    {
        return $this->map(['POST'], $uri, $callback);
    }

    public function put(string $uri, $callback)
    {
        return $this->map(['PUT'], $uri, $callback);
    }

    public function patch(string $uri, $callback)
    {
        return $this->map(['PATCH'], $uri, $callback);
    }

    public function delete(string $uri, $callback)
    {
        return $this->map(['DELETE'], $uri, $callback);
    }

    public function options(string $uri, $callback)
    {
        return $this->map(['OPTIONS'], $uri, $callback);
    }

    public function any(string $uri, $callback)
    {
        return $this->map(['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'], $uri, $callback);
    }

    public function map(array $methods, $uri, $callback)
    {
        $methodStr = implode(',', $methods);
        if (!($this->group_prefix == '/' or $this->group_prefix == '')) {
            $uri = rtrim($this->group_prefix, '/').'/'.ltrim($uri, '/');
        }
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