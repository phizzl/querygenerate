<?php


namespace Phizzl\QueryGenerate;


use Phizzl\QueryGenerate\Factory\FactoryInterface;
use Phizzl\QueryGenerate\Tables\TableInterface;

class QueryGenerator
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * QueryGenerator constructor.
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory){
        $this->factory = $factory;
    }

    /**
     * @param string $name
     * @param array $options
     * @return TableInterface
     */
    public function table($name, array $options = array()){
        return $this->factory->getTable($name, $options);
    }
}