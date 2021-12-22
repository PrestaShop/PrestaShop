<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Routing\Linter\AdminRouteProvider;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ioTableheaders = ['Invalid routes', 'Valid routes suggestions'];
        $ioTableRows = [];
        /** @var Route $route */
        foreach ($this->adminRouteProvider->getRoutes() as $routeName => $route) {
            try {
                $this->namingConventionLinter->lint($routeName, $route);
            } catch (NamingConventionException $e) {
                $ioTableRows[] = [$routeName, $e->getExpectedRouteName()];
            }
        }

        $io = new SymfonyStyle($input, $output);

        if (!empty($ioTableRows)) {
            $io->title('PrestaShop routes follow admin_{resources}_{action} naming convention structure');
            $io->warning(sprintf(
                '%s routes are not following naming conventions:',
                count($ioTableRows)
            ));
            $io->table($ioTableheaders, $ioTableRows);

            return 0;
        }

        $io->success('Admin routes and controllers follow naming conventions.');

        return 1;
    }
}
