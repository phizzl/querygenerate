<?php


namespace Phizzl\QueryGenerate\Tables;


use Phizzl\QueryGenerate\Factory\FactoryInterface;

class Column implements ColumnInterface
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
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * Column constructor.
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory){
        $this->factory = $factory;
        $this->name = "";
        $this->type = "";
        $this->options = array();
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
     */
    public function setType($type){
        $this->type = $type;
        return $this;
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
    public function generate(){
        return $this->factory->getDriver()->generateColumn($this);
    }

    /**
     * @inheritdoc
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(){
        return $this->options;
    }
}