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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Checks if all admin routes are configured with _legacy_link
 */
class LegacyLinkLinterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:linter:legacy-link')
            ->setDescription('Checks if _legacy_link is configured in BackOffice routes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unconfiguredRoutes = $this->getUnconfiguredRoutes();
        $io = new SymfonyStyle($input, $output);

        if (!empty($unconfiguredRoutes)) {
            $io->warning(sprintf(
                '%s routes are not configured with _legacy_link:',
                count($unconfiguredRoutes)
            ));
            $io->listing($unconfiguredRoutes);

            return;
        }

        $io->success('There is no routes without _legacy_link settings');
    }

    /**
     * Returns routes that are missing _legacy_link configuration
     *
     * @return array
     */
    private function getUnconfiguredRoutes()
    {
        $legacyLinkLinter = $this->getContainer()->get('prestashop.bundle.routing.linter.legacy_link_linter');
        $adminRouteProvider = $this->getContainer()->get('prestashop.bundle.routing.linter.admin_route_provider');
        $routes = $adminRouteProvider->getRoutes();
        $unconfiguredRoutes = [];

        foreach ($routes as $routeName => $route) {
            if (true === $legacyLinkLinter->lint('_legacy_link', $route)) {
                continue;
            }
            $unconfiguredRoutes[] = $routeName;
        }

        return $unconfiguredRoutes;
    }
}
