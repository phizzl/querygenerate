<?php


namespace Phizzl\QueryGenerate\Factory;


use Phizzl\QueryGenerate\Drivers\DriverInterface;
use Phizzl\QueryGenerate\Tables\ColumnInterface;
use Phizzl\QueryGenerate\Tables\TableInterface;

interface FactoryInterface
{
    /**
     * Set the driver to generate the queries
     *
     * @param DriverInterface $driver
     */
    public function setDriver(DriverInterface $driver);

    /**
     * Gets the driver for generating the queries
     *
     * @return DriverInterface
     */
    public function getDriver();

    /**
     * Gets a new instance of a table
     *
     * @param string $name
     * @param array $options
     * @return TableInterface
     */
    public function getTable($name, array $options = array());

    /**
     * Gets a new instance of a table column
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @return ColumnInterface
     */
    public function getColumn($name, $type, array $options = array());
}