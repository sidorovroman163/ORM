<?php

namespace PHPixieTests\ORM\Models\Type\Database\Implementation;

/**
 * @coversDefaultClass \PHPixie\ORM\Model\Type\Database\Implementation\Repository
 */
abstract class RepositoryTest extends \PHPixieTests\AbstractORMTest
{
    protected $models;
    protected $database;
    protected $configData = array(
        'idField' => 'fairy_id',
        'connection' => 'test',
        'modelName'  => 'fairy'
    );
    protected $config;
    
    protected $repository;
    protected $loadData;
    
    public function setUp()
    {
        $this->models = $this->quickMock('\PHPixie\ORM\Models');
        $this->database = $this->quickMock('\PHPixie\ORM\Database');
        $this->config = $this->config();
        $this->loadData = new \stdClass;
        
        $this->repository = $this->repository();
    }
    
    /**
     * @covers ::__construct
     * @covers PHPixie\ORM\Models\Implementation\Repository::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::modelName
     * @covers ::<protected>
     */
    public function testModelName()
    {
        $this->assertEquals($this->configData['modelName'], $this->repository->modelName());
    }
    
    
    /**
     * @covers ::connection
     * @covers ::<protected>
     */
    public function testConnection()
    {
        $connection = $this->prepareConnection();
        $this->assertSame($connection, $this->repository->connection());
    }
    
    /**
     * @covers ::delete
     * @covers ::<protected>
     */
    public function testDelete()
    {
        $this->deleteTest();
        $this->deleteTest(true);
    }
    
    /**
     * @covers ::save
     * @covers ::<protected>
     */
    public function testSave()
    {
        $connection = $this->prepareConnection();
        $this->saveTest($connection, true);
        $this->saveTest($connection, false);
    }
    
    /**
     * @covers ::delete
     * @covers ::<protected>
     */
    public function testDeleteException()
    {
        $entity = $this->getEntity();
        $this->method($entity, 'isDeleted', true);
        $this->setExpectedException('\PHPixie\ORM\Exception\Entity');
        $this->repository->delete($entity);
    }
    
    /**
     * @covers ::save
     * @covers ::<protected>
     */
    public function testSaveException()
    {
        $entity = $this->getEntity();
        $this->method($entity, 'isDeleted', true);
        $this->setExpectedException('\PHPixie\ORM\Exception\Entity');
        $this->repository->save($entity);
    }
    
    /**
     * @covers ::databaseSelectQuery
     * @covers ::databaseUpdateQuery
     * @covers ::databaseDeleteQuery
     * @covers ::databaseInsertQuery
     * @covers ::databaseCountQuery
     * @covers ::<protected>
     */
    public function testQueries()
    {
        $types = array('select', 'update', 'delete', 'insert', 'count');
        $connection = $this->prepareConnection();
        foreach($types as $type) {
            $query = $this->prepareDatabaseQuery($type, $connection);
            $method = 'database'.ucfirst($type).'Query';
            $this->assertSame($query, $this->repository->$method());
        }
    }
    
    protected function prepareQuery($modelsOffset = 0)
    {
        $query = $this->getQuery();
        $this->method($this->models, 'query', $query, array($this->configData['modelName']), $modelsOffset);
        return $query;
    }
    
    protected function prepareEntity($isNew = true, $data = null, $modelsOffset = 0)
    {
        $entity = $this->getEntity();
        $data = $this->prepareBuildData($data);
        $this->method($this->models, 'entity', $entity, array($this->modelName, $isNew, $data), $modelsOffset);
        return $entity;
    }
    
    protected function prepareDatabaseQuery($type, $connection = null, $connectionOffset = 0)
    {
        if($connection === null) {
            $connection = $this->prepareConnection();
        }
        $query = $this->getDatabaseQuery($type);
        $this->method($connection, $type.'Query', $query, array(), $connectionOffset);
        $this->prepareSetQuerySource($query);
        return $query;
    }
    
    protected function deleteTest($isNew = false)
    {
        $entity = $this->getEntity();
        $this->method($entity, 'isDeleted', false);
        
        $this->method($entity, 'isNew', $isNew);
        if(!$isNew) {
            $query = $this->prepareQuery();
            $this->method($query, 'in', $query, array($entity), 0);
            $this->method($query, 'delete', null, array(), 1);
        }
        $this->method($entity, 'setIsDeleted', null, array(true), 'once');
        
        $this->repository->delete($entity);
    }
    
   protected function saveTest($connection, $isNew = false)
    {
        $entity = $this->getEntity();
        $data = $this->getData();
        $this->method($entity, 'isDeleted', false, array(), 0);
        $this->method($entity, 'data', $data, array(), 1);
        $this->method($entity, 'isNew', $isNew, array(), 2);

        $dataOffset = 0;
        $connectionOffset = 0;

        if($isNew) {
            $this->prepareInsertEntityData($connection, $data, $dataOffset, $connectionOffset);

            $this->method($connection, 'insertId', 4, array(), $connectionOffset);
            $this->method($entity, 'setField', null, array($this->configData['idField'], 4), 3);
            $this->method($entity, 'setId', null, array(4), 4);
            $this->method($entity, 'setIsNew', null, array(false), 5);

        }else{
            $this->method($entity, 'id', 3, array(), 3);
            $this->prepareUpdateEntityData($connection, 3, $data, $dataOffset, $connectionOffset);

        }

        $this->method($data, 'setCurrentAsOriginal', null, array(), $dataOffset);
        $this->repository->save($entity);
    }
    
    protected function prepareInsertEntityData($connection, $data, &$dataOffset = 0, &$connectionOffset = 0)
    {
        $query = $this->prepareDatabaseQuery('insert', $connection, $connectionOffset++);
            
        $dataArray = array(5);
        $this->method($data, 'data', (object) $dataArray, array(), $dataOffset++);
        $this->method($query, 'data', $query, array($dataArray), 1);
        $this->method($query, 'execute', null, array(), 2);
    }
    
    public function prepareConnection()
    {
        $connection = $this->getConnection();
        $this->method($this->database, 'connection', $connection, array($this->configData['connection']));
        return $connection;
    }
    
    protected function config()
    {
        $config = $this->getConfig();
        foreach($this->configData as $key => $value) {
            $config->$key = $value;
        }
        return $config;
    }
    
    abstract protected function prepareUpdateEntityData($connection, $id, $data, &$dataOffset = 0, &$connectionOffset = 0);
    abstract protected function getConnection();
    abstract protected function getDatabaseQuery($type);
    abstract protected function prepareSetQuerySource($query);
    
}