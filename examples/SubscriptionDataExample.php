<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">

<?php

use BigDataTable\Data\ScalarData;
use BigDataTable\DataGroup;
use BigDataTable\Parser\TwigParser;
use BigDataTable\Record;
use BigDataTable\Table;

$start = microtime(true);

define('ROOT', dirname(__DIR__));

require_once ROOT . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    require_once ROOT . '/src/' . str_replace('\\', '/', $class) . '.php';
});

$twigParser = new TwigParser();
$table = new Table($twigParser, ['diffYear' => 2019, 'showDiff' => true,]);

$subscriptions = $table->createGroup('Subscriptions');
$newPaidSubscriptions = $subscriptions->addChild(new DataGroup('New paid subscriptions', 'This is a description', ['sum' => true, 'showPercentageDiff' => true]));
foreach (['Starter', 'Plus', 'Premium'] as $product) {
    /** @var DataGroup $data */
    $data = $newPaidSubscriptions->addData(new ScalarData($product));
//$data->setSumType(\BigDataTable\Data::SUM_TYPE_AVG);
    $i = 1;
    for ($year = 2013; $year <= 2020; $year++) {
        for ($month = 1; $month <= 12 && ($year != date('Y') || $month <= date('m')); $month++) {
            $i++;
            $date = new DateTime();
            $date->setDate($year, $month, 1);
            $date->setTime(0, 0, 0);
            $data->addRecord(new Record($date, rand(0, 200)));
        }
    }
}

$newUnpaidSubscriptions = $subscriptions->addChild(new DataGroup('New free subscriptions', '', ['sum' => true]));
foreach (['Free', 'Trial'] as $product) {
    /** @var DataGroup $data */
    $data = $newUnpaidSubscriptions->addData(new ScalarData($product));
//$data->setSumType(\BigDataTable\Data::SUM_TYPE_AVG);
    $i = 1;
    for ($year = 2013; $year <= 2020; $year++) {
        for ($month = 1; $month <= 12 && ($year != date('Y') || $month <= date('m')); $month++) {
            $i++;
            $date = new DateTime();
            $date->setDate($year, $month, 1);
            $date->setTime(0, 0, 0);
            $data->addRecord(new Record($date, rand(0, 200)));
        }
    }
}


$renewals = $table->createGroup('Renewals');
$paidRenewals = $renewals->addChild(new DataGroup('Paid renewals', '', ['sum' => true]));
foreach (['Starter', 'Plus', 'Premium'] as $product) {
    /** @var DataGroup $data */
    $data = $paidRenewals->addData(new ScalarData($product));
//$data->setSumType(\BigDataTable\Data::SUM_TYPE_AVG);
    $i = 1;
    for ($year = 2013; $year <= 2020; $year++) {
        for ($month = 1; $month <= 12 && ($year != date('Y') || $month <= date('m')); $month++) {
            $i++;
            $date = new DateTime();
            $date->setDate($year, $month, 1);
            $date->setTime(0, 0, 0);
            $data->addRecord(new Record($date, rand(0, 200)));
        }
    }
}

$unpaidRenewals = $renewals->addChild(new DataGroup('Free renewals', '', ['sum' => true]));
foreach (['Free', 'Trial'] as $product) {
    /** @var DataGroup $data */
    $data = $unpaidRenewals->addData(new ScalarData($product));
//$data->setSumType(\BigDataTable\Data::SUM_TYPE_AVG);
    $i = 1;
    for ($year = 2013; $year <= 2020; $year++) {
        for ($month = 1; $month <= 12 && ($year != date('Y') || $month <= date('m')); $month++) {
            $i++;
            $date = new DateTime();
            $date->setDate($year, $month, 1);
            $date->setTime(0, 0, 0);
            $data->addRecord(new Record($date, rand(0, 200)));
        }
    }
}


//var_dump($data->getSumByYear(2020));die();
//print json_encode($table, JSON_PRETTY_PRINT) . "\n";
print $table->parse();

$elapsed = microtime(true) - $start;
print 'Elapsed time: ' . $elapsed;

?>

</div>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

</body>
</html>