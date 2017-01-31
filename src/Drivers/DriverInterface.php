<?php


namespace Phizzl\QueryGenerate\Drivers;


use Phizzl\QueryGenerate\Tables\ColumnInterface;
use Phizzl\QueryGenerate\Tables\TableInterface;

interface DriverInterface
{
    /**
     * Set The query escaper
     *
     * @param QueryEscapeInterface $queryEscape
     */
    public function setQueryEscape(QueryEscapeInterface $queryEscape);

    /**
     * Generate the parts for the table
     *
     * @param TableInterface $table
     * @return string
     */
    public function generateTable(TableInterface $table);

    /**
     * Generate the part for the column
     *
     * @param ColumnInterface $column
     * @return string
     */
    public function generateColumn(ColumnInterface $column);

    /**
     * @param TableInterface $table
     * @return string
     */
    public function generateData(TableInterface $table);
}