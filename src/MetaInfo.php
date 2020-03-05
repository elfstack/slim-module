<?php
namespace ElfStack\SlimModule;

class MetaInfo
{
    protected string $defaultKey;
    protected array $config;

    public function __construct($key, $config)
    {
        $this->defaultKey = $key;
        $this->config = $config;
    }

    public function getDefaultKey()
    {
        return $this->defaultKey;
    }

    public function getNamespacePrefix()
    {
        return rtrim($this->config['namespace'] ?? '', '\\').'\\';
    }

    public function getApiPrefix()
    {
        return $this->config['api_prefix'] ?? '';
    }

    public function getRoutes()
    {
        return $this->config['apis'] ?? '';
    }

    public function getServices()
    {
        return $this->config['services'] ?? '';
    }
}