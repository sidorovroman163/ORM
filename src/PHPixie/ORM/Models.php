<?php

namespace PHPixie\ORM;

class Models
{
    protected $ormBuilder;
    protected $configSlice;
    protected $wrappers;
    
    protected $databaseModel;
    protected $embeddedModel;
    
    public function __construct($ormBuilder, $configSlice, $wrappers = null)
    {
        $this->ormBuilder   = $ormBuilder;
        $this->configSlice  = $configSlice;
        $this->wrappers     = $wrappers;
    }
    
    public function modelConfigSlice($modelName)
    {
        return $this->configSlice->slice($modelName);
    }
    
    public function wrappers()
    {
        return $this->wrappers;
    }
    
    public function database()
    {
        if($this->databaseModel === null)
        {
            $this->databaseModel = $this->buildDatabaseModel();
        }
        
        return $this->databaseModel;
    }
    
    public function embedded()
    {
        if($this->embeddedModel === null)
        {
            $this->embeddedModel = $this->buildEmbeddedModel();
        }
        
        return $this->embeddedModel;
    }
    
    protected function buildDatabaseModel()
    {
        return new \PHPixie\ORM\Models\Type\Database(
            $this,
            $this->ormBuilder->configs(),
            $this->ormBuilder->database(),
            $this->ormBuilder->drivers()
        );
    }
    
    protected function buildEmbeddedModel()
    {
        return new \PHPixie\ORM\Models\Type\Embedded(
            $this,
            $this->ormBuilder->configs(),
            $this->ormBuilder->data(),
            $this->ormBuilder->maps()
        );
    }
}