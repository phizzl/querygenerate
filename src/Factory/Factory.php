<?php


namespace Phizzl\QueryGenerate\Factory;


use Phizzl\QueryGenerate\Drivers\DriverInterface;
use Phizzl\QueryGenerate\Tables\Column;
use Phizzl\QueryGenerate\Tables\Table;

class Factory implements FactoryInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @inheritdoc
     */
    public function setDriver(DriverInterface $driver){
        $this->driver = $driver;
    }

    /**
     * @inheritdoc
     */
    public function getDriver(){
        return $this->driver;
    }

    /**
     * @inheritdoc
     */
    public function getTable($name, array $options = array()){
        $table = new Table($this);
        $table->setName($name);
        $table->setOptions($options);

        return $table;
    }

    /**
     * @inheritdoc
     */
    public function getColumn($name, $type, array $options = array()){
        $column = new Column($this);
        $column->setName($name);
        $column->setType($type);
        $column->setOptions($options);

        return $column;
    }
}