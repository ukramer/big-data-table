<?php declare(strict_types=1);

namespace BigDataTable\Parser;

use BigDataTable\Parser;
use BigDataTable\Table;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;

class TwigParser extends Parser
{
    private $twig;

    public function __construct()
    {
        $rootPath = dirname(__DIR__);
        $loader = new FilesystemLoader($rootPath . '/View/Twig');
        $this->twig = new Environment($loader, [
            'cache' => false,
        ]);
    }

    function getName(): string
    {
        return 'Twig';
    }

    public function parse(Table $table): string
    {
        $vars = [
            'years' => $table->getYears(),
            'months' => $table->getMonths(),
            'cssClass' => $table->getCssClass(),
            'showDiff' => $table->showDiff(),
            'diffYear' => $table->getDiffYear(),
            'dataGroups' => $table->getDataGroups(),
        ];
        try {
            $ret = $this->twig->render('Table.twig', $vars);
        } catch (Error $e) {
            throw new \Exception('Parsing error by Twig: ' . $e->getMessage());
        }
        return $ret;
    }
}
