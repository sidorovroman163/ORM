<?php

namespace PHPixie\ORM\Relationship;

abstract class Side
{
    protected $config;
    protected $type;

    public function __construct($type, $config)
    {
        $this->type = $type;
        $this->config = $config;
    }

    public function type()
    {
        return $this->type;
    }

    public function config()
    {
        return $this->config;
    }

    abstract public function modelName();
    abstract public function propertyName();
    abstract public function relationship();
	abstract public function handleDeletions();
}