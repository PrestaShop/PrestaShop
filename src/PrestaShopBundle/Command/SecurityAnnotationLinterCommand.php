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

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;

/**
 * Checks if all admin routes have @AdminSecurity configured
 */
final class SecurityAnnotationLinterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('prestashop:linter:security-annotation')
            ->setDescription('Checks if Back Office route controllers has configured Security annotations.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adminRouteProvider = $this->getContainer()->get('prestashop.bundle.routing.linter.admin_route_provider');
        $securityAnnotationLinter = $this->getContainer()
            ->get('prestashop.bundle.routing.linter.security_annotation_linter');

        $notConfiguredRoutes = [];

        /** @var Route $route */
        foreach ($adminRouteProvider->getRoutes() as $routeName => $route) {
            try {
                $securityAnnotationLinter->lint($routeName, $route);
            } catch (LinterException $e) {
                $notConfiguredRoutes[] = $routeName;
            }
        }

        $io = new SymfonyStyle($input, $output);

        if (!empty($notConfiguredRoutes)) {
            $io->warning(sprintf(
                '%s routes are not configured with @AdminSecurity annotation:',
                count($notConfiguredRoutes)
            ));
            $io->listing($notConfiguredRoutes);

            return;
        }

        $io->success('Admin routes has @AdminSecurity configured.');
    }
}
