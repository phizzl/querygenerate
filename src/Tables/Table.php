<?php


namespace Phizzl\QueryGenerate\Tables;


use Phizzl\QueryGenerate\Factory\FactoryInterface;
use Phizzl\QueryGenerate\GeneratorException;

class Table implements TableInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $addColumns;

    /**
     * @var array
     */
    private $changeColumns;

    /**
     * @var array
     */
    private $removedColumns;

    /**
     * @var bool
     */
    private $isCreated;

    /**
     * @var array
     */
    private $addedIndexes;

    /**
     * @var array
     */
    private $removedIndexes;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $primaryKey;

    /**
     * @var bool
     */
    private $removePrimaryKey;

    /**
     * @var string
     */
    private $rename;

    /**
     * @var array
     */
    private $insertData;

    /**
     * Table constructor.
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory){
        $this->factory = $factory;
        $this->name = "";
        $this->addColumns = array();
        $this->changeColumns = array();
        $this->removedColumns = array();
        $this->isCreated = false;
        $this->addedIndexes = array();
        $this->removedIndexes = array();
        $this->options = array();
        $this->primaryKey = array();
        $this->removePrimaryKey = false;
        $this->rename = "";
        $this->insertData = array();
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     * @return $this
     * @throws GeneratorException
     */
    public function addColumn($name, $type, array $options = array()){
        if(isset($this->changeColumns[$name])
            || isset($this->addColumns[$name])){
            throw new GeneratorException("Column \"{$name}\" is already being changed or added");
        }

        $this->addColumns[$name] = $this->factory->getColumn($name, $type, $options);

        return $this;
    }

    /**
     * @inheritdoc
     * @return $this
     * @throws GeneratorException
     */
    public function changeColumn($name, $type, array $options = array()){
        if(isset($this->changeColumns[$name])
            || isset($this->addColumns[$name])){
            throw new GeneratorException("Column \"{$name}\" is already being changed or added");
        }

        $this->changeColumns[$name] = $this->factory->getColumn($name, $type, $options);

        return $this;
    }

    public function removeColumn($name){
        $this->removedColumns[] = $name;
    }


    /**
     * @inheritdoc
     */
    public function getAddedColumns(){
        return $this->addColumns;
    }

    /**
     * @inheritdoc
     */
    public function getChangedColumns(){
        return $this->changeColumns;
    }

    /**
     * @return array
     */
    public function getRemovedColumns(){
        return $this->removedColumns;
    }

    /**
     * @inheritdoc
     */
    public function generate(){
        return $this->factory->getDriver()->generateTable($this);
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function setIsCreated($isCreated){
        $this->isCreated = $isCreated;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsCreated(){
        return $this->isCreated;
    }

    /**
     * @inheritdoc
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAddedIndexes(){
        return $this->addedIndexes;
    }

    /**
     * @inheritdoc
     * @return $this
     * @throws GeneratorException
     */
    public function addIndex(array $columnNames){
        $index = $this->getIndexName($columnNames);
        if(in_array($index, $this->removedIndexes)){
            throw new GeneratorException("The index \"$index\" is already marked for being removed (Table \"{$this->getName()}\")");
        }
        $this->addedIndexes[$index] = $columnNames;

        return $this;
    }

    /**
     * @return array
     */
    public function getRemovedIndexes(){
        return $this->removedIndexes;
    }

    /**
     * @inheritdoc
     * @return $this
     * @throws GeneratorException
     */
    public function removeIndex(array $columnNames){
        $index = $this->getIndexName($columnNames);
        if(isset($this->addedIndexes[$index])){
            throw new GeneratorException("The index \"$index\" is already marked for being added (Table \"{$this->getName()}\")");
        }
        $this->removedIndexes[] = $index;

        return $this;
    }

    /**
     * @param array $columnNames
     * @return string
     */
    private function getIndexName(array $columnNames){
        return implode('_', $columnNames) . '_idx';
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function setOptions(array $options){
        $this->options = $options;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(){
        return $this->options;
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function setPrimaryKey(array $columnNames){
        $this->primaryKey = $columnNames;
        return $this;
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function removePrimaryKey(){
        $this->removePrimaryKey = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKey(){
        return $this->primaryKey;
    }

    /**
     * @inheritdoc
     */
    public function isPrimaryKeyRemoved(){
        return $this->removePrimaryKey;
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function rename($newName){
        $this->rename = $newName;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRename(){
        return $this->rename;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addInsertData(array $data){
        $this->insertData[] = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getInsertData(){
        return $this->insertData;
    }
}