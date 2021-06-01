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
use PrestaShopBundle\Routing\Linter\SecurityAnnotationLinter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;

/**
 * Checks if all admin routes have @AdminSecurity configured
 *
 * @see \PrestaShopBundle\Security\Annotation\AdminSecurity
 */
final class SecurityAnnotationLinterCommand extends ContainerAwareCommand
{
    public const ACTION_LIST_ALL = 'list';
    public const ACTION_FIND_MISSING = 'find-missing';

    /**
     * @param string $expression
     *
     * @return string
     */
    public static function parseExpression($expression)
    {
        $pattern1 = '#\[(.*)\]#';
        $pattern2 = '#is_granted\((.*),#';
        $matches1 = [];
        $matches2 = [];
        preg_match($pattern1, $expression, $matches1);

        if (count($matches1) > 1) {
            return $matches1[1];
        }
        preg_match($pattern2, $expression, $matches2);
        if (count($matches2) > 1) {
            return $matches2[1];
        }

        return '';
    }

    /**
     * @return string[]
     */
    public static function getAvailableActions()
    {
        return [self::ACTION_LIST_ALL, self::ACTION_FIND_MISSING];
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $description = 'Checks if Back Office route controllers has configured Security annotations.';
        $actionDescription = sprintf(
            'Action to perform, must be one of: %s',
            implode(', ', self::getAvailableActions())
        );

        $this
            ->setName('prestashop:linter:security-annotation')
            ->setDescription($description)
            ->addArgument('action', InputArgument::REQUIRED, $actionDescription);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionToPerform = $input->getArgument('action');

        if (!in_array($actionToPerform, self::getAvailableActions())) {
            throw new \InvalidArgumentException(sprintf(
                    'Action must be one of: %s',
                    implode(', ', self::getAvailableActions())
                )
            );
        }

        switch ($actionToPerform) {
            case self::ACTION_LIST_ALL:
                $this->listAllRoutesAndRelatedPermissions($input, $output);
                break;
            case self::ACTION_FIND_MISSING:
                $this->findRoutesWithMissingSecurityAnnotations($input, $output);
                break;

            default:
                throw new \RuntimeException(sprintf('Unknown action %s', $actionToPerform));
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function listAllRoutesAndRelatedPermissions(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $adminRouteProvider = $container
            ->get('prestashop.bundle.routing.linter.admin_route_provider');
        /** @var SecurityAnnotationLinter $securityAnnotationLinter */
        $securityAnnotationLinter = $container
            ->get('prestashop.bundle.routing.linter.security_annotation_linter');

        $listing = [];

        foreach ($adminRouteProvider->getRoutes() as $routeName => $route) {
            /* @var Route $route */
            try {
                $annotation = $securityAnnotationLinter->getRouteSecurityAnnotation($routeName, $route);
                $listing[] = [
                    $route->getDefault('_controller'),
                    implode(', ', $route->getMethods()),
                    'Yes',
                    self::parseExpression($annotation->getExpression()),
                ];
            } catch (LinterException $e) {
                $listing[] = [
                    $route->getDefault('_controller'),
                    implode(', ', $route->getMethods()),
                    'No',
                    '',
                ];
            }
        }

        $io = new SymfonyStyle($input, $output);
        $headers = ['Controller action', 'Methods', 'Is secured ?', 'Permissions'];

        $io->table($headers, $listing);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function findRoutesWithMissingSecurityAnnotations(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $adminRouteProvider = $container
            ->get('prestashop.bundle.routing.linter.admin_route_provider');
        /** @var SecurityAnnotationLinter $securityAnnotationLinter */
        $securityAnnotationLinter = $container
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

        $io->success('All admin routes are secured with @AdminSecurity.');
    }
}
