<?php
namespace ElfStack\SlimModule;

class MetaInfo
{
    protected string $defaultKey;
    protected array $config;
    protected string $from;

    public function __construct($key, $config)
    {
        $this->defaultKey = $key;
        $this->config = $config;
        $this->from = '(inline `MetaInfo` object)';
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