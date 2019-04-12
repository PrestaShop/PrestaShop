<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;

/**
 * Runs naming conventions linter in the CLI
 */
final class NamingConventionLinterCommand extends ContainerAwareCommand
{
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
        $adminRouteProvider = $this->getContainer()->get('prestashop.bundle.routing.linter.admin_route_provider');
        $namingConventionLinter = $this->getContainer()
            ->get('prestashop.bundle.routing.linter.naming_convention_linter');

        $invalidRoutes = [];

        /** @var Route $route */
        foreach ($adminRouteProvider->getRoutes() as $routeName => $route) {
            try {
                $namingConventionLinter->lint($routeName, $route);
            } catch (LinterException $e) {
                $invalidRoutes[] = $routeName;
            }
        }

        $io = new SymfonyStyle($input, $output);

        if (!empty($invalidRoutes)) {
            $io->warning(sprintf(
                '%s routes are not following naming conventions:',
                count($invalidRoutes)
            ));
            $io->listing($invalidRoutes);

            return;
        }

        $io->success('Admin routes and controllers follow naming conventions.');
    }
}
