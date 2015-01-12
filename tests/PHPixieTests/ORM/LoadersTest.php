<?php

namespace PHPixieTests\ORM;

/**
 * @coversDefaultClass \PHPixie\ORM\Loaders
 */
class LoadersTest extends \PHPixieTests\AbstractORMTest
{
    protected $models;
    
    protected $loaders;
    
    protected $embeddedModel;
    
    public function setUp()
    {
        $this->models = $this->quickMock('\PHPixie\ORM\Models');
        $this->embeddedModel = $this->quickMock('\PHPixie\ORM\Models\Type\Embedded');
        
        $this->method($this->models, 'embedded', $this->embeddedModel, array());
        
        $this->loaders = new \PHPixie\ORM\Loaders($this->models);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::iterator
     */
    public function testIterator()
    {
        $loader = $this->quickMock('\PHPixie\ORM\Loaders\Loader');
        $iterator = $this->loaders->iterator($loader);
        
        $this->assertInstance($iterator, '\PHPixie\ORM\Loaders\Iterator', array(
            'loader' => $loader
        ));
    }
    
    /**
     * @covers ::multiplePreloader
     */
    public function testMultiplePreloader()
    {
        $preloader = $this->abstractMock('\PHPixie\ORM\Relationships\Relationship\Implementation\Preloader\Result\Multiple');
        $ids = array(1, 2, 3);
        $loader = $this->loaders->multiplePreloader($preloader, $ids);
        
        $this->assertInstance($loader, '\PHPixie\ORM\Loaders\Loader\MultiplePreloader', array(
            'loaders' => $this->loaders,
            'multiplePreloader' => $preloader,
            'ids' => $ids
        ));
    }
    
    /**
     * @covers ::editableProxy
     * @covers ::cachingProxy
     * @covers ::preloadingProxy
     */
    public function testProxies()
    {
        $loader = $this->quickMock('\PHPixie\ORM\Loaders\Loader');
        foreach(array('editable', 'caching', 'preloading') as $type) {
            $method = $type.'Proxy';
            $proxy = $this->loaders->$method($loader);
            
            $this->assertInstance($proxy, '\PHPixie\ORM\Loaders\Loader\Proxy\\'.ucfirst($type), array(
                'loaders' => $this->loaders,
                'loader' => $loader
            ));
        }
    }
    
    /**
     * @covers ::reusableResult
     */
    public function testReusableResult()
    {
        $repository = $this->quickMock('\PHPixie\ORM\Loaders\Loader');
        $reusableResult = $this->quickMock('\PHPixie\ORM\Steps\Result\Reusable');
            
        $loader = $this->loaders->reusableResult($repository, $reusableResult);

        $this->assertInstance($loader, '\PHPixie\ORM\Loaders\Loader\Repository\ReusableResult', array(
            'loaders' => $this->loaders,
            'repository' => $repository,
            'reusableResult' => $reusableResult,
        ));
    }
     
    /**
     * @covers ::dataIterator
     */
    public function testDataIterator()
    {
        $repository = $this->quickMock('\PHPixie\ORM\Loaders\Loader');
        $iterator = new \ArrayIterator();
            
        $dataIterator = $this->loaders->dataIterator($repository, $iterator);
        
        $this->assertInstance($dataIterator, '\PHPixie\ORM\Loaders\Loader\Repository\DataIterator', array(
            'loaders' => $this->loaders,
            'repository' => $repository,
            'dataIterator' => $iterator,
        ));
    }
    
    /**
     * @covers ::arrayNode
     */
    public function testArrayNode()
    {
        $arrayNode = $this->quickMock('\PHPixie\ORM\Data\Types\Document\Node\ArrayNode');
        $owner = $this->quickMock('\PHPixie\ORM\Models\Type\Embedded\Entity');
        $ownerPropertyName = 'flowers';
            
        $loader = $this->loaders->arrayNode($arrayNode, $owner, $ownerPropertyName);
        
        $this->assertInstance($loader,'\PHPixie\ORM\Loaders\Loader\Embedded\ArrayNode', array(
            'loaders' => $this->loaders,
            'arrayNode' => $arrayNode,
            'owner' => $owner,
            'ownerPropertyName' => $ownerPropertyName,
        ));
    }
    
    protected function assertInstance($object, $class, $propertyMap)
    {
        $this->assertInstanceOf($class, $object);
        foreach($propertyMap as $name => $value) {
            $this->assertAttributeEquals($value, $name, $object);
        }
    }
}