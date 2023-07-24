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
use Context;
use Dispatcher;
use Link;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tab;
use Tools;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/nav_bar.html.twig')]
class NavBar
{
    private Link $link;

    public function __construct(
        private readonly LegacyContext $context,
        private readonly LoggerInterface $logger,
    ) {
        $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $this->link = new Link($protocol_link, $protocol_content);
    }

    public function getToggleNavigationUrl(): string
    {
        return $this->link->getAdminLink('AdminEmployees', true, [], [
            'action' => 'toggleMenu',
        ]);
    }

    public function getDefaultTabLink(): string
    {
        return $this->link->getAdminLink(Tab::getClassNameById((int) Context::getContext()->employee->default_tab));
    }

    public function getPsVersion(): string
    {
        return _PS_VERSION_;
    }

    public function isCollapseMenu(): bool
    {
        return $this->context->cookie->collapse_menu ?? false;
    }

    public function getTabs(): array
    {
        return $this->buildTabs();
    }

    private function buildTabs($parentId = 0, $level = 0): array
    {
        $tabs = Tab::getTabs(Context::getContext()->language->id, $parentId);
        $currentId = Tab::getCurrentParentId();
        $controllerName = Dispatcher::getInstance()->getController();

        $filteredTabs = array_filter($tabs, function ($tab) {
            return $this->isValidTab($tab);
        });

        $processedTabs = array_map(function ($tab) use ($currentId, $level, $controllerName) {
            return $this->processTab($tab, $currentId, $level, $controllerName);
        }, $filteredTabs);

        return array_values(array_filter($processedTabs));
    }

    private function isValidTab(array $tab): bool
    {
        return Tab::checkTabRights($tab['id_tab'])
            && $tab['enabled']
            && !($tab['class_name'] === 'AdminStock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
            && $tab['class_name'] !== 'AdminCarrierWizard';
    }

    private function processTab(array $tab, int $currentId, int $level, string $controllerName): array
    {
        $isCurrentTab = ($currentId === $tab['id_tab']) || ($tab['class_name'] === $controllerName);

        $tab['current'] = $isCurrentTab;
        if ($isCurrentTab) {
            $tab['current_level'] = $level;
        }
        $tab['img'] = null;

        try {
            $tab['href'] = $this->link->getTabLink($tab);
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

    private function getTabLinkFromSubTabs(array $subtabs)
    {
        foreach ($subtabs as $tab) {
            if ($tab['active'] && $tab['enabled']) {
                return $tab['href'];
            }
        }

        return '';
    }
}
