<?php


namespace Phizzl\QueryGenerate\Tables;


use Phizzl\QueryGenerate\GeneratableInterface;

interface ColumnInterface extends GeneratableInterface
{
    /**
     * Set the column name
     *
     * @param string $name
     * @return mixed
     */
    public function setName($name);

    /**
     * Get the column name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the column type
     *
     * @param string $type
     * @return mixed
     */
    public function setType($type);

    /**
     * Gets the column type
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the column options
     *
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * Gets the column options
     *
     * @return array
     */
    public function getOptions();
}