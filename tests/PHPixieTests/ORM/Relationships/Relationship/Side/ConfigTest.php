<?php

namespace PHPixieTests\ORM\Relationships\Relationship\Side;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Relationship\Side\Config
 */
abstract class ConfigTest extends \PHPixieTests\AbstractORMTest
{
    protected $config;
    protected $inflector;
    protected $plural = array();
    protected $sets = array();
    
    public function setUp()
    {
        $this->inflector = $this->quickMock('\PHPixie\ORM\Inflector');
        
        $plural = $this->plural;
        $singular = array_flip($this->plural);
        
        $this->inflector
            ->expects($this->any())
            ->method('plural')
            ->will($this->returnCallback(function($key) use($plural){
                return $plural[$key];
            }));
        
        $this->inflector
            ->expects($this->any())
            ->method('singular')
            ->will($this->returnCallback(function($key) use($singular){
                return $singular[$key];
            }));
        
        $this->config = $this->getConfig($this->sets[0][0]);
    }
    
    /**
     * @covers ::__construct
     * @covers \PHPixie\ORM\Relationship\Side\Config::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        foreach($this->sets as $set) {
            $config = $this->getConfig($set[0]);
            foreach($set[1] as $key => $value)
                $this->assertEquals($value, $config->$key);
        }
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        $data = $this->sets[0][1];
        $key = key($data);
        $value = value($data);
        $this->assertEquals($value, $this->config->get($key));
    }
    
    protected function slice($data)
    {
        $slice = $this->quickMock('\PHPixie\Config\Slice');
        $slice
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function($key) use($data){
                return $data[$key];
            }));
        return $slice;
    }
    
    abstract protected function getConfig($slice);
}