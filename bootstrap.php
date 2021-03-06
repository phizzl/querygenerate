<?php

use Phizzl\QueryGenerate\Drivers\MysqlDriver;
use Phizzl\QueryGenerate\Drivers\MysqlQueryEscape;
use Phizzl\QueryGenerate\Factory\Factory;
use Phizzl\QueryGenerate\QueryGenerator;

$loader = require_once __DIR__ . '/vendor/autoload.php';

$driver = new MysqlDriver();
$driver->setQueryEscape(new MysqlQueryEscape());
$factory = new Factory();
$factory->setDriver($driver);

$generator = new QueryGenerator($factory);
var_dump($generator
    ->table('test', array('engine' => 'InnoDB', 'collation' => 'utf8_general_ci'))
    ->setIsCreated(true)
    ->addColumn('OXID', 'char', array('length' => 32, 'collation' => 'latin1_general_ci'))
    ->addColumn('test', 'int', array('length' => 11, 'autoincrement' => true))
    ->changeColumn('Spalte 1', 'TINYINT', array('rename' => 'Spalte Spass', 'length' => 1))
    ->addIndex(array('test'))
    ->setPrimaryKey(array('OXID'))
    ->generate());