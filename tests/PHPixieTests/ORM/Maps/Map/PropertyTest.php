<?php

namespace PHPixieTests\ORM\Maps\Map;

/**
 * @coversDefaultClass \PHPixie\ORM\Maps\Map\Property
 */
abstract class PropertyTest extends \PHPixieTests\ORM\Maps\MapTest
{    
    protected $relationships;
    
    public function setUp()
    {
        $this->relationships = $this->quickMock('\PHPixie\ORM\Relationships');
        parent::setUp();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    protected function prepareRelationship($type, $relationshipsAt = 0)
    {
        $relationship = $this->getRelationship();
        $this->method($this->relationships, 'get', $relationship, array($type), $relationshipsAt);
        return $relationship;
    }
    
    protected function getRelationship()
    {
        return $this->abstractMock('\PHPixie\ORM\Relationships\Relationship');
    }
}