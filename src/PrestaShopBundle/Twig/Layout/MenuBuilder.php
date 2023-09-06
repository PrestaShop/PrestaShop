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

namespace PrestaShopBundle\Twig\Layout;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilder
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly RequestStack $requestStack,
        private readonly TabRepository $tabRepository,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LegacyParametersConverter $legacyParametersConverter
    ) {
    }

    public function getCurrentTab(): ?Tab
    {
        $tab = null;
        $routeName = $this->getRouteName();
        if (!empty($routeName)) {
            $tab = $this->tabRepository->findOneByRouteName($routeName);
        }

        if (!$tab) {
            $className = $this->getLegacyControllerClassName();
            if (!empty($className)) {
                $tab = $this->tabRepository->findOneByClassName($className);
            }
        }

        return $tab;
    }

    public function getAncestorsTab(int $currentTabId): array
    {
        return $this->tabRepository->getAncestors($currentTabId);
    }

    /**
     * @return array<string, MenuLink>
     */
    public function getBreadcrumbLinks(): array
    {
        $currentTab = $this->getCurrentTab();
        if (null === $currentTab) {
            return [];
        }

        $tabAncestors = $this->getAncestorsTab($currentTab->getId());

        return $this->convertTabsToBreadcrumbLinks($currentTab, $tabAncestors);
    }

    /**
     * @return array<string, MenuLink>
     */
    public function convertTabsToBreadcrumbLinks(Tab $currentTab, array $tabAncestors): array
    {
        $breadcrumbLinks = [
            'tab' => $this->convertTabToMenuLink($currentTab),
        ];

        if (!empty($tabAncestors)) {
            $breadcrumbLinks['container'] = $this->convertTabToMenuLink(reset($tabAncestors));
        }

        $actionLink = $this->getActionLink();
        if (null !== $actionLink) {
            $breadcrumbLinks['action'] = $actionLink;
        }

        return $breadcrumbLinks;
    }

    private function convertTabToMenuLink(Tab $tab): MenuLink
    {
        return new MenuLink(
            name: $this->getBreadcrumbLabel($tab),
            href: $this->getLinkFromTab($tab),
            icon: 'icon-' . $tab->getClassName(),
        );
    }

    /**
     * @return array<int, MenuLink>
     */
    public function buildNavigationTabs(Tab $tab): array
    {
        $currentLevelTabs = $this->tabRepository->findByParentId($tab->getIdParent());
        $navigationTabs = [];

        /* @var $currentLevelTab Tab */
        foreach ($currentLevelTabs as $currentLevelTab) {
            $tabLang = $currentLevelTab->getTabLangByLanguageId($this->getContextLanguageId());
            $menuLink = new MenuLink(
                name: $tabLang ? $tabLang->getName() : $currentLevelTab->getWording(),
                href: $this->getLinkFromTab($currentLevelTab),
                attributes: [
                    'id_tab' => $currentLevelTab->getId(),
                    'class_name' => $currentLevelTab->getClassName(),
                    'current' => $currentLevelTab->getId() == $tab->getId(),
                    'active' => $currentLevelTab->getActive(),
                ]
            );
            $navigationTabs[] = $menuLink;
        }

        return $navigationTabs;
    }

    private function getBreadcrumbLabel(Tab $tab): string
    {
        if (null !== $tab->getWording() && null !== $tab->getWordingDomain()) {
            return $this->translator->trans($tab->getWording(), [], $tab->getWordingDomain());
        }

        $tabLang = $tab->getTabLangByLanguageId($this->getContextLanguageId());
        if (null !== $tabLang) {
            return $tabLang->getName();
        }

        if (!$tab->getTabLangs()->isEmpty()) {
            return $tab->getTabLangs()->first()->getName();
        }

        return $tab->getClassName();
    }

    private function getContextLanguageId(): int
    {
        return $this->context->getLanguage()->getId();
    }

    private function getActionLink(): ?MenuLink
    {
        $action = $this->getLegacyAction();

        switch (true) {
            // In legacy no action is always equivalent to list action, but maybe we should change this into null and consider no action can be identified
            case null === $action:
            case $action === '':
            case str_starts_with($action, 'list'):
                return new MenuLink(
                    name: $this->translator->trans('List', [], 'Admin.Actions'),
                    icon: 'icon-th-list'
                );

            case str_starts_with($action, 'add'):
                return new MenuLink(
                    name: $this->translator->trans('Add', [], 'Admin.Actions'),
                    icon: 'icon-plus'
                );
            case str_starts_with($action, 'edit'):
            case str_starts_with($action, 'update'):
                return new MenuLink(
                    name: $this->translator->trans('Edit', [], 'Admin.Actions'),
                    icon: 'icon-pencil'
                );

            case str_starts_with($action, 'details'):
            case str_starts_with($action, 'view'):
                return new MenuLink(
                    name: $this->translator->trans('View details', [], 'Admin.Actions'),
                    icon: 'icon-zoom-in'
                );
            case str_starts_with($action, 'options'):
                return new MenuLink(
                    name: $this->translator->trans('Options', [], 'Admin.Actions'),
                    icon: 'icon-cogs'
                );
            case str_starts_with($action, 'generator'):
                return new MenuLink(
                    name: $this->translator->trans('Generator', [], 'Admin.Actions'),
                    icon: 'icon-flask'
                );
            default:
                return null;
        }
    }

    private function getLegacyAction(): ?string
    {
        $legacyParameters = $this->legacyParametersConverter->getParameters(
            $this->requestStack->getMainRequest()->attributes->all(),
            $this->requestStack->getMainRequest()->query->all()
        );

        return $legacyParameters['action'] ?? null;
    }

    public function getLegacyControllerClassName(): ?string
    {
        $request = $this->requestStack->getMainRequest();
        if ($request->attributes->has('_legacy_controller')) {
            return $request->attributes->get('_legacy_controller');
        } elseif ($request->query->has('controller')) {
            return $request->query->get('controller');
        }

        return null;
    }

    private function getRouteName(): ?string
    {
        $request = $this->requestStack->getMainRequest();
        if ($request->attributes->has('_route')) {
            return $request->attributes->get('_route');
        }

        return null;
    }

    private function getLinkFromTab(Tab $tab): string
    {
        if (!empty($tab->getRouteName())) {
            $href = $this->urlGenerator->generate($tab->getRouteName());
        } else {
            $href = $this->context->getAdminLink($tab->getClassName());
        }

        return $href;
    }
}
