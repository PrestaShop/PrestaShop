<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Routing\Linter\AdminRouteProvider;
use PrestaShopBundle\Routing\Linter\Exception\ControllerNotFoundException;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
use PrestaShopBundle\Routing\Linter\Exception\SymfonyControllerConventionException;
use PrestaShopBundle\Routing\Linter\NamingConventionLinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;

/**
 * Runs naming conventions linter in the CLI
 */
final class NamingConventionLinterCommand extends Command
{
    /**
     * @var AdminRouteProvider
     */
    private $adminRouteProvider;

    /**
     * @var NamingConventionLinter
     */
    private $namingConventionLinter;

    public function __construct(AdminRouteProvider $adminRouteProvider, NamingConventionLinter $namingConventionLinter)
    {
        parent::__construct();
        $this->adminRouteProvider = $adminRouteProvider;
        $this->namingConventionLinter = $namingConventionLinter;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('prestashop:linter:naming-convention')
            ->setDescription('Checks if Back Office routes and controllers follow naming convention.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $invalidRouteNameRows = [];
        $invalidControllerRows = [];
        $controllerNotFoundRows = [];
        /** @var Route $route */
        foreach ($this->adminRouteProvider->getRoutes() as $routeName => $route) {
            try {
                $this->namingConventionLinter->lint($routeName, $route);
            } catch (NamingConventionException $e) {
                $invalidRouteNameRows[] = [$routeName, $e->getExpectedRouteName()];
            } catch (SymfonyControllerConventionException $e) {
                $invalidControllerRows[] = [$routeName, $e->getInvalidController()];
            } catch (ControllerNotFoundException $e) {
                $controllerNotFoundRows[] = [$routeName, $e->getInvalidController()];
            }
        }

        $io = new SymfonyStyle($input, $output);

        if (!empty($invalidRouteNameRows) || !empty($invalidControllerRows)) {
            $this->displayInvalidRoutes($invalidRouteNameRows, $io);
            $this->displayInvalidControllers($invalidControllerRows, $io);
            $this->displayNotFoundControllers($controllerNotFoundRows, $io);

            return 1;
        }

        $io->success('Admin routes and controllers follow naming conventions.');

        return 0;
    }

    private function displayInvalidRoutes(array $invalidRouteNameRows, SymfonyStyle $io): void
    {
        $this->displayInvalidRows(
            $invalidRouteNameRows,
            'PrestaShop routes follow admin_{resources}_{action} naming convention structure',
            '%s routes are not following naming conventions:',
            ['Invalid routes', 'Valid routes suggestions'],
            $io
        );
    }

    private function displayInvalidControllers(array $invalidControllerRows, SymfonyStyle $io): void
    {
        $this->displayInvalidRows(
            $invalidControllerRows,
            'Symfony controller naming convention follows FQCN::actionName',
            '%s routes are not following controller conventions:',
            ['Invalid routes', 'Invalid controller convention'],
            $io
        );
    }

    private function displayNotFoundControllers(array $controllerNotFoundRows, SymfonyStyle $io): void
    {
        $this->displayInvalidRows(
            $controllerNotFoundRows,
            'Symfony controller was not found',
            '%s routes are using controller not found:',
            ['Invalid routes', 'Controller not found'],
            $io
        );
    }

    private function displayInvalidRows(array $invalidRows, string $title, string $warning, array $header, SymfonyStyle $io): void
    {
        if (empty($invalidRows)) {
            return;
        }

        $io->title($title);
        $io->warning(sprintf(
            $warning,
            count($invalidRows)
        ));
        $io->table($header, $invalidRows);
    }
}
