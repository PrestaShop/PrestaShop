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

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tab;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/nav_bar.html.twig')]
class NavBar
{
    protected ?array $tabs = null;

    public function __construct(
        protected readonly LegacyContext $context,
        protected readonly LoggerInterface $logger,
        protected readonly MenuBuilder $menuBuilder,
        protected readonly string $psVersion,
    ) {
    }

    public function getDefaultTab(): string
    {
        return Tab::getClassNameById((int) $this->context->getContext()->employee->default_tab);
    }

    public function getPsVersion(): string
    {
        return $this->psVersion;
    }

    public function getTabs(): array
    {
        if (null === $this->tabs) {
            $this->tabs = $this->buildTabs();
        }

        return $this->tabs;
    }

    protected function buildTabs($parentId = 0, $level = 0): array
    {
        $tabs = Tab::getTabs($this->context->getContext()->language->id, $parentId);
        $currentId = Tab::getCurrentParentId();
        $controllerName = $this->menuBuilder->getLegacyControllerClassName();

        $filteredTabs = array_filter($tabs, function ($tab) {
            return $this->isValidTab($tab);
        });

        $processedTabs = array_map(function ($tab) use ($currentId, $level, $controllerName) {
            return $this->processTab($tab, $currentId, $level, $controllerName);
        }, $filteredTabs);

        return array_values(array_filter($processedTabs));
    }

    protected function isValidTab(array $tab): bool
    {
        return Tab::checkTabRights($tab['id_tab'])
            && $tab['enabled']
            && !($tab['class_name'] === 'AdminStock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
            && $tab['class_name'] !== 'AdminCarrierWizard';
    }

    protected function processTab(array $tab, int $currentId, int $level, string $controllerName): array
    {
        $isCurrentTab = ($currentId === $tab['id_tab']) || ($tab['class_name'] === $controllerName);

        $tab['current'] = $isCurrentTab;
        if ($isCurrentTab) {
            $tab['current_level'] = $level;
        }
        $tab['img'] = null;

        try {
            $tab['href'] = $this->context->getContext()->link->getTabLink($tab);
        } catch (RouteNotFoundException $e) {
            $this->logger->warning(
                sprintf('Route not found in one of the Tab %s', $tab['route_name'] ?? ''),
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
            $tab['href'] = '';
        }

        $tab['sub_tabs'] = array_values($this->buildTabs($tab['id_tab'], $level + 1));

        $subTabHref = $this->getTabLinkFromSubTabs($tab['sub_tabs']);

        if (!empty($subTabHref)) {
            $tab['href'] = $subTabHref;
        } elseif ($tab['id_parent'] === 0 && empty($tab['icon'])) {
            return [];
        } elseif (empty($tab['icon'])) {
            $tab['icon'] = 'extension';
        }

        foreach ($tab['sub_tabs'] as $subTab) {
            if ($subTab['current']) {
                $tab['current'] = true;
                $tab['current_level'] = $subTab['current_level'];
                break;
            }
        }

        return $tab;
    }

    protected function getTabLinkFromSubTabs(array $subtabs)
    {
        foreach ($subtabs as $tab) {
            if ($tab['active'] && $tab['enabled']) {
                return $tab['href'];
            }
        }

        return '';
    }
}
