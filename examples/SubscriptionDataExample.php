<?php

use BigDataTable\Data\ScalarData;
use BigDataTable\Table;

$start = microtime(true);

define('ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    require_once ROOT . '/src/' . str_replace('\\', '/', $class) . '.php';
});

$table = new Table();

$subscriptions = $table->createGroup('Subscriptions');
$data = $subscriptions->addData(new ScalarData('Plus'));
for ($year = 2013; $year <= 2020; $year++) {
    for ($month = 1; $month <= 12; $month++) {
        $date = new DateTime();
        $date->setDate($year, $month, 1);
        $date->setTime(0, 0, 0);
        $data->addRecord(new \BigDataTable\Record($date, 0));
    }
}

print json_encode($table, JSON_PRETTY_PRINT) . "\n";

$elapsed = microtime(true) - $start;
print 'Elapsed time: ' . $elapsed;
