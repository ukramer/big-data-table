<?php declare(strict_types=1);

namespace BigDataTable\Parser;

use BigDataTable\Parser;
use BigDataTable\Table;
use Exception;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;

/**
 * ScalarData is used to parse integer amounts.
 *
 * There is currently no special functionality needed than the functionality that is provided by Data.
 * @see Data
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
class TwigParser extends Parser
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * Contains the configuration for the Twig Environment and instantiates the Environment and the
     * FilesystemLoader to load the templates.
     */
    public function __construct()
    {
        $rootPath = dirname(__DIR__);
        $loader = new FilesystemLoader($rootPath . '/View/Twig');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    function getName(): string
    {
        return 'Twig';
    }

    /**
     * @inheritDoc
     */
    public function parse(Table $table): string
    {
        $vars = [
            'currentYear' => intval(date('Y')),
            'currentMonth' => intval(date('m')),
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
            throw new Exception('Parsing error by Twig: ' . $e->getMessage());
        }
        return $ret;
    }
}
