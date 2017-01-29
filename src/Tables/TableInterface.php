<?php


namespace Phizzl\QueryGenerate\Tables;


use Phizzl\QueryGenerate\GeneratableInterface;

interface TableInterface extends GeneratableInterface
{
    /**
     * Set if the table will be created.
     * Set to false if it already exists
     *
     * @param bool $isCreated
     * @return mixed
     */
    public function setIsCreated($isCreated);

    /**
     * Get if the table should be created
     *
     * @return bool
     */
    public function getIsCreated();

    /**
     * Set the table name
     *
     * @param string $name
     * @return mixed
     */
    public function setName($name);

    /**
     * Get the table name
     *
     * @return string
     */
    public function getName();

    /**
     * Adds a column to the table
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @return mixed
     */
    public function addColumn($name, $type, array $options = array());

    /**
     * Adds a column to the table
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @return mixed
     */
    public function changeColumn($name, $type, array $options = array());

    /**
     * Removes a column from a table
     *
     * @param $name
     * @return mixed
     */
    public function removeColumn($name);

    /**
     * Gets the added columns
     *
     * @return array|\Iterator
     */
    public function getAddedColumns();

    /**
     * Gets the changed columns
     *
     * @return array|\Iterator
     */
    public function getChangedColumns();

    /**
     * Gets the removed columns
     *
     * @return array|\Iterator
     */
    public function getRemovedColumns();

    /**
     * Adds an index to the table
     *
     * @param array $columNames
     * @return mixed
     */
    public function addIndex(array $columNames);

    /**
     * @return array|\Iterator
     */
    public function getAddedIndexes();

    /**
     * Adds an index to the table
     *
     * @param array $columnNames
     * @return mixed
     */
    public function removeIndex(array $columnNames);

    /**
     * @return array|\Iterator
     */
    public function getRemovedIndexes();

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

    /**
     * Adds an primary key
     *
     * @param array $columnNames
     * @return mixed
     */
    public function setPrimaryKey(array $columnNames);

    /**
     * Removes the primary key
     *
     * @return mixed
     */
    public function removePrimaryKey();

    /**
     * Return a list of field names to build the PK
     *
     * @return array
     */
    public function getPrimaryKey();

    /**
     * Tells wether the PK is being removed or not
     *
     * @return bool
     */
    public function isPrimaryKeyRemoved();
}