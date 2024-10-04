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

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\MenuLink;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/toolbar.html.twig')]
class Toolbar
{
    protected string $title = '';

    protected string $subTitle = '';
    protected string $helpLink = '';
    protected bool $sidebarEnabled = true;
    protected int $currentTabLevel = 0;

    /**
     * @var array<int, MenuLink>
     */
    protected array $navigationTabs = [];

    /**
     * @var array<string, MenuLink>
     */
    protected array $breadcrumbs = [];

    protected array $layoutHeaderToolbarBtn = [];

    public function __construct(
        protected readonly HookDispatcherInterface $hookDispatcher,
        protected readonly MenuBuilder $menuBuilder,
    ) {
    }

    public function mount(string $layoutTitle, string $helpLink, bool $enableSidebar, string $layoutSubTitle, array $layoutHeaderToolbarBtn, array $breadcrumbLinks = []): void
    {
        $this->sidebarEnabled = $enableSidebar;
        $this->helpLink = $helpLink;
        $this->layoutHeaderToolbarBtn = $layoutHeaderToolbarBtn;
        $currentTab = $this->menuBuilder->getCurrentTab();
        $tabs = [];
        $ancestorsTab = [];
        if (null !== $currentTab) {
            $tabs[] = $currentTab;
            $ancestorsTab = $this->menuBuilder->getAncestorsTab($currentTab->getId());
            if (!empty($ancestorsTab)) {
                $tabs[] = $ancestorsTab;
                $this->currentTabLevel = count($ancestorsTab);

                if ($this->currentTabLevel >= 3) {
                    $this->navigationTabs = $this->menuBuilder->buildNavigationTabs($currentTab);
                }
            }
        }

        if (!empty($breadcrumbLinks)) {
            $this->setBreadcrumbs($breadcrumbLinks, $tabs);
        } elseif ($currentTab !== null) {
            $this->setBreadcrumbs($this->menuBuilder->convertTabsToBreadcrumbLinks($currentTab, $ancestorsTab), $tabs);
        }

        $this->setTitle($layoutTitle);
        $this->subTitle = $layoutSubTitle;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubTitle(): string
    {
        return $this->subTitle;
    }

    public function getCurrentTabLevel(): int
    {
        return $this->currentTabLevel;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function getNavigationTabs(): array
    {
        return $this->navigationTabs;
    }

    public function isSidebarEnabled(): bool
    {
        return $this->sidebarEnabled;
    }

    public function getHelpLink(): string
    {
        return $this->helpLink;
    }

    public function getLayoutHeaderToolbarBtn(): array
    {
        return $this->layoutHeaderToolbarBtn;
    }

    protected function setTitle(string $layoutTitle): void
    {
        if (empty($layoutTitle) && isset($this->breadcrumbs['tab'])) {
            $this->title = $this->breadcrumbs['tab']->name;
        } else {
            $this->title = $layoutTitle;
        }
    }

    /**
     * @param MenuLink[] $breadcrumbs
     * @param Tab[] $tabs
     *
     * @return void
     */
    protected function setBreadcrumbs(array $breadcrumbs, array $tabs): void
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->hookDispatcher->dispatchWithParameters('actionAdminBreadcrumbModifier', ['tabs' => $tabs, 'breadcrumb' => &$this->breadcrumbs]);
    }
}
