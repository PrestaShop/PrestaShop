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
        $className = $this->getLegacyControllerClassName();
        if (null === $className) {
            return null;
        }

        return $this->tabRepository->findOneByClassName($className);
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

        $breadcrumbLinks = [
            'tab' => $this->convertTabToMenuLink($currentTab),
        ];

        $tabAncestors = $this->tabRepository->getAncestors($currentTab->getId());
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
        if (!empty($tab->getRouteName())) {
            $url = $this->urlGenerator->generate($tab->getRouteName());
        } else {
            $url = $this->context->getAdminLink($tab->getClassName());
        }

        return new MenuLink(
            name: $this->getBreadcrumbLabel($tab),
            url: $url,
            icon: 'icon-' . $tab->getClassName(),
        );
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

    private function getLegacyControllerClassName(): ?string
    {
        return $this->requestStack->getMainRequest()->attributes->get('_legacy_controller');
    }
}
