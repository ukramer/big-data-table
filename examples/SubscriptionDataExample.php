<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="style.css"/>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>

    <style>
        html, body {
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <?php

    use BigDataTable\Data;
    use BigDataTable\Data\PercentageData;
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
        /** @var Data $data */
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
        /** @var Data $data */
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
        /** @var Data $data */
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
        /** @var Data $data */
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

    /** @var Data $conversionRate */
    $conversionRate = $subscriptions->addChild(new PercentageData('Conversion rate', 'Description here', ['invertColor' => true]));
    $conversionRate->setSumType(PercentageData::SUM_TYPE_AVG);
    $date = new DateTime();
    $date->setDate(2020, 1, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 4));
    $date = new DateTime();
    $date->setDate(2020, 2, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 10));
    $date = new DateTime();
    $date->setDate(2020, 3, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 10));
    $date = new DateTime();
    $date->setDate(2020, 4, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 20));
    $date->setDate(2019, 1, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 2));
    $date = new DateTime();
    $date->setDate(2019, 2, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 20));
    $date = new DateTime();
    $date->setDate(2019, 3, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 8));
    $date = new DateTime();
    $date->setDate(2019, 4, 1);
    $date->setTime(0, 0, 0);
    $conversionRate->addRecord(new Record($date, 60));

    //var_dump($data->getSumByYear(2020));die();
    //print json_encode($table, JSON_PRETTY_PRINT) . "\n";
    try {
        print $table->parse();
    } catch (Exception $e) {
        print 'Exception while parsing: ' . $e->getMessage() . "\n";
    }

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