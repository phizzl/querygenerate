QueryGenerate
==============

This is a library to generate plain SQL statements by describing the table in PHP.

Example:
```php
$loader = require_once __DIR__ . '/vendor/autoload.php';

$driver = new MysqlDriver();
$driver->setQueryEscape(new MysqlQueryEscape());
$factory = new Factory();
$factory->setDriver($driver);

$generator = new QueryGenerator($factory);
echo $generator
    ->table('test', array('engine' => 'InnoDB', 'collation' => 'utf8_general_ci'))
    ->setIsCreated(true)
    ->addColumn('OXID', 'char', array('length' => 32, 'collation' => 'latin1_general_ci'))
    ->addColumn('test', 'int', array('length' => 11, 'autoincrement' => true))
    ->changeColumn('Spalte 1', 'TINYINT', array('rename' => 'Spalte Spass', 'length' => 1))
    ->addIndex(array('test'))
    ->setPrimaryKey(array('OXID'))
    ->generate();
```