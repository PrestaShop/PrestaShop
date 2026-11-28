<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Tab\ModuleTabRegister;
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
        $className = Tab::getClassNameById((int) $this->context->getContext()->employee->default_tab);

        if (!$className) {
            $className = 'AdminDashboard';
        }

        return $className;
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
        $currentId = (int) Tab::getCurrentParentId();

        if ($currentId === -1) {
            $currentId = $this->menuBuilder->getCurrentTab()?->getId() ?: -1;
        }

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
            && $tab['class_name'] !== 'AdminCarrierWizard';
    }

    protected function processTab(array $tab, int $currentId, int $level, ?string $controllerName): array
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
        } elseif ($this->isUnreachableTopLevelSection($tab)) {
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

    /**
     * A top level entry with no linkable child leads nowhere. An icon is enough to keep one that
     * owns a page, but the catch-all parent that module tabs attach to owns none - linking to it
     * lands on a controller that does not exist.
     *
     * @param array<string, mixed> $tab
     */
    protected function isUnreachableTopLevelSection(array $tab): bool
    {
        if ($tab['id_parent'] !== 0) {
            return false;
        }

        return empty($tab['icon'])
            || ModuleTabRegister::DEFAULT_PARENT_CLASS_NAME === $tab['class_name'];
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
