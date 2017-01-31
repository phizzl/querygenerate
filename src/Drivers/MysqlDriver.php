<?php


namespace Phizzl\QueryGenerate\Drivers;


use Phizzl\QueryGenerate\GeneratorException;
use Phizzl\QueryGenerate\Tables\ColumnInterface;
use Phizzl\QueryGenerate\Tables\TableInterface;

class MysqlDriver implements DriverInterface
{
    /**
     * @var array
     */
    private static $allowedColumnTypes = array(
        'TINYINT', 'SMALLINT', 'INT', 'BIGINT', 'BIT', 'FLOAT', 'DOUBLE', 'DECIMAL',
        'CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT',
        'BINARY', 'VARBINARY', 'TINYBLOB', 'BLOB', 'MEDIUMBLOB', 'LONGBLOB',
        'DATE', 'TIME', 'YEAR', 'DATETIME', 'TIMESTAMP',
        'ENUM', 'SET'
    );

    /**
     * @var array
     */
    private static $lengthRequired = array(
        'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'BIT', 'DECIMAL',
        'CHAR', 'VARCHAR',
        'BINARY', 'VARBINARY',
        'YEAR'
    );

    /**
     * @var array
     */
    private static $signable = array(
        'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL'
    );

    /**
     * @var array
     */
    private static $autoIncrementable = array(
        'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'
    );

    /**
     * @var array
     */
    private static $valueRequired = array(
        'SET', 'ENUM'
    );

    /**
     * @var QueryEscapeInterface
     */
    private $queryEscape;

    /**
     * @param mixed $queryEscape
     */
    public function setQueryEscape(QueryEscapeInterface $queryEscape){
        $this->queryEscape = $queryEscape;
    }

    /**
     * @inheritdoc
     */
    public function generateTable(TableInterface $table){
        if(!$table->getIsCreated()){
            return $this->createTable($table);
        }

        return $this->changeTable($table);
    }

    /**
     * @param TableInterface $table
     * @return string
     */
    private function changeTable(TableInterface $table){
        $queries = array();
        $tableOptions = $table->getOptions();
        $removedIndexStatements = $this->getRemoveIndexesStatements($table);
        $addedIndexStatements = $this->getAddedIndexesStatements($table);
        $changedColumnStatements = $this->getChangedColumnStatements($table);
        $addedColumnStatements = $this->getAddedColumnStatements($table);
        $removedColumnStatements = $this->getRemovedColumnStatements($table);

        $baseQuery = "ALTER TABLE " . $this->queryEscape->escapeFieldName($table->getName()) . "\n";

        if(isset($tableOptions['engine'])){
            $queries[] = $baseQuery . "ENGINE={$tableOptions['engine']}";
        }

        if(count($removedIndexStatements)){
            $queries[] = $baseQuery . implode(",\n", $removedIndexStatements);
        }

        if(count($removedColumnStatements)){
            $queries[] = $baseQuery . implode(",\n", $removedColumnStatements);
        }

        if(count($changedColumnStatements)){
            $queries[] = $baseQuery . implode(",\n", $changedColumnStatements);
        }

        if(count($addedColumnStatements)){
            $queries[] = $baseQuery . implode(",\n", $addedColumnStatements);
        }

        if(count($addedIndexStatements)){
            $queries[] = $baseQuery . implode(",\n", $addedIndexStatements);
        }

        if($table->isPrimaryKeyRemoved()){
            $queries[] = $baseQuery . "REMOVE PRIMARY KEY";
        }

        if(count($table->getPrimaryKey())){
            $queries[] = $baseQuery . "ADD PRIMARY KEY (" . $this->queryEscape->escapeFieldName($table->getPrimaryKey()) . ")";
        }

        if($table->getRename()){
            $queries[] = "RENAME " . $this->queryEscape->escapeFieldName($table->getName())
                . " TO " . $this->queryEscape->escapeFieldName($table->getRename());
        }

        return implode(";\n", $queries) . ";";
    }

    /**
     * @param TableInterface $table
     * @return array
     */
    private function getRemovedColumnStatements(TableInterface $table){
        $statements = array();
        /* @var ColumnInterface $column */
        foreach($table->getRemovedColumns() as $columnName){
            $statements[] = "DROP COLUMN " . $this->queryEscape->escapeFieldName($columnName);
        }

        return $statements;
    }

    /**
     * @param TableInterface $table
     * @return array
     */
    private function getAddedColumnStatements(TableInterface $table){
        $statements = array();
        /* @var ColumnInterface $column */
        foreach($table->getAddedColumns() as $column){
            $columnOptions = $column->getOptions();
            $name = $column->getName();

            $statements[] = "ADD COLUMN " . $this->queryEscape->escapeFieldName($name) . " " . $column->generate();
        }

        return $statements;
    }

    /**
     * @param TableInterface $table
     * @return array
     */
    private function getChangedColumnStatements(TableInterface $table){
        $statements = array();
        /* @var ColumnInterface $column */
        foreach($table->getChangedColumns() as $column){
            $columnOptions = $column->getOptions();
            $name = $column->getName();
            $newName = isset($columnOptions['rename']) ? $columnOptions['rename'] : $name;

            $statements[] = "CHANGE COLUMN " . $this->queryEscape->escapeFieldName($name) . " " . $column->generate();
        }

        return $statements;
    }

    /**
     * @param TableInterface $table
     * @return array
     */
    private function getAddedIndexesStatements(TableInterface $table){
        $statements = array();
        foreach($table->getAddedIndexes() as $indexName => $columnNames){
            $statements[] = "ADD INDEX " . $this->queryEscape->escapeFieldName($indexName)
                . " (" . $this->queryEscape->escapeFieldName($columnNames) . ")";
        }

        return $statements;
    }

    /**
     * @param TableInterface $table
     * @return array
     */
    private function getRemoveIndexesStatements(TableInterface $table){
        $statements = array();
        foreach($table->getRemovedIndexes() as $indexName){
            $statements[] = "DROP INDEX " . $this->queryEscape->escapeFieldName($indexName);
        }

        return $statements;
    }

    /**
     * @inheritdoc
     * @throws GeneratorException
     */
    private function createTable(TableInterface $table){
        $tableOptions = $table->getOptions();
        $columnDefinitions = array();
        $indexDefinitions = array();
        $autoincrementDefined = false;

        /* @var ColumnInterface $column */
        foreach($table->getAddedColumns() as $column){
            $columnOptions = $column->getOptions();
            $columnDefinition = $column->generate();

            if(isset($columnOptions['autoincrement'])
                && $columnOptions['autoincrement'] === true){
                if($autoincrementDefined){
                    throw new GeneratorException("There can only be one autoincrement column (\"{$column->getName()}\")");
                }

                if(!$this->isColumnInIndex($table, $column)) {
                    throw new GeneratorException("The autoincrement column \"{$column->getName()}\" needs to be an index");
                }

                $autoincrementDefined = true;
            }

            $columnDefinitions[$column->getName()] = $columnDefinition;
        }

        foreach($table->getAddedIndexes() as $indexName => $columnNames){
            foreach($columnNames as $columnName){
                if(!in_array($columnName, array_keys($columnDefinitions))){
                    throw new GeneratorException("Using undefined column \"{$columnName}\" for index on \"{$table->getName()}\"");
                }
            }

            $indexDefinitions[] = "INDEX " . $this->queryEscape->escapeFieldName($indexName) .
                " (" . $this->queryEscape->escapeFieldName($columnNames) . ")";
        }

        $sql = "CREATE TABLE " . $this->queryEscape->escapeFieldName($table->getName()) . " (\n";
        $sql .= implode(",\n", $columnDefinitions);
        $sql .= count($indexDefinitions) ? ",\n" . implode(",\n", $indexDefinitions) : "";
        $sql .= count($table->getPrimaryKey())
            ? ",\nPRIMARY KEY (" . $this->queryEscape->escapeFieldName($table->getPrimaryKey()) . ")"
            : "";
        $sql .= "\n)\n";

        $sql .= isset($tableOptions['collation']) ? "COLLATE='{$tableOptions['collation']}'\n" : "";
        $sql .= isset($tableOptions['engine']) ? "ENGINE={$tableOptions['engine']}\n" : "";
        $sql .= isset($tableOptions['autoincrement']) ? "AUTO_INCREMENT={$tableOptions['autoincrement']}\n" : "";

        return "{$sql};";
    }

    /**
     * Check if a column is marked as index
     *
     * @param TableInterface $table
     * @param ColumnInterface $column
     * @return bool
     */
    private function isColumnInIndex(TableInterface $table, ColumnInterface $column){
        $indexes = $table->getAddedIndexes();

        if(in_array($column->getName(), $table->getPrimaryKey())){
            return true;
        }

        foreach($indexes as $columnNames){
            if(in_array($column->getName(), $columnNames)){
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     * @throws GeneratorException
     */
    public function generateColumn(ColumnInterface $column){
        $name = $column->getName();
        $type = strtoupper($column->getType());
        $options = $column->getOptions();

        if(!in_array($type, static::$allowedColumnTypes)){
            throw new GeneratorException("Type \"{$type}\" is not supported (column \"{$name}\")");
        }

        if(in_array($type, static::$lengthRequired)
            && !isset($options['length'])){
            throw new GeneratorException("Length required for type \"{$type}\" (column \"{$name}\")");
        }

        if(!in_array($type, static::$lengthRequired)
            && isset($options['length'])){
            throw new GeneratorException("Length not supported for type \"{$type}\" (column \"{$name}\")");
        }

        if(!in_array($type, static::$signable)
            && isset($options['signed'])){
            throw new GeneratorException("The type \"{$type}\" cannot be signed (column \"{$name}\")");
        }

        if(!in_array($type, static::$signable)
            && isset($options['zerofill'])){
            throw new GeneratorException("The type \"{$type}\" cannot be zerofilled (column \"{$name}\")");
        }

        if(!in_array($type, static::$autoIncrementable)
            && isset($options['autoincrement'])){
            throw new GeneratorException("The type \"{$type}\" cannot be auto incremented (column \"{$name}\")");
        }

        if(in_array($type, static::$valueRequired)
            && !isset($options['values'])){
            throw new GeneratorException("The type \"{$type}\" needs values to be defined (column \"{$name}\")");
        }

        $str = $this->queryEscape->escapeFieldName(isset($options['rename']) ? $options['rename'] : $name) . " {$type}";
        $str .= isset($options['length']) ? "({$options['length']})" : "";
        $str .= isset($options['values']) ? $this->generateValues($options['values']) : "";
        $str .= isset($options['signed']) && $options['signed'] === false ? " UNSIGNED" : "";
        $str .= isset($options['zerofill']) && $options['zerofill'] === true ? " ZEROFILL" : "";
        $str .= isset($options['nullable']) && $options['nullable'] ? " NULL" : " NOT NULL";
        $str .= isset($options['default']) ? " DEFAULT '{$options['default']}'" : "";
        $str .= isset($options['autoincrement']) && $options['autoincrement'] === true ? " AUTO_INCREMENT" : "";
        $str .= isset($options['comment']) ? " COMMENT " . $this->queryEscape->escapeValue($options['comment']) : "";
        $str .= isset($options['collation']) ? " COLLATE '{$options['collation']}'" : "";

        return $str;
    }

    /**
     * @param string|array $values
     * @return string
     */
    private function generateValues($values){
        if(is_string($values)){
            $values = array($values);
        }

        return $this->queryEscape->escapeValue($values);
    }

}