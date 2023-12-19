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

use Context;
use Media;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\TemplateVariables;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tools;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/head_tag.html.twig')]
class HeadTag
{
    protected string $metaTitle;

    public function __construct(
        protected readonly LegacyContext $context,
        protected readonly Configuration $configuration,
        protected readonly MenuBuilder $menuBuilder,
        protected readonly TranslatorInterface $translator,
        protected readonly HookDispatcherInterface $hookDispatcher,
        protected readonly TemplateVariables $templateVariables,
        protected readonly CountryContext $countryContext,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly LanguageContext $defaultLanguageContext,
        protected readonly LegacyControllerContext $legacyControllerContext,
    ) {
    }

    public function mount(string $metaTitle): void
    {
        if (empty($metaTitle)) {
            $breadcrumbs = $this->menuBuilder->getBreadcrumbLinks();
            if (empty($breadcrumbs)) {
                $this->metaTitle = '';
            } else {
                $this->metaTitle = $breadcrumbs['tab']->name;
            }
        } else {
            $this->metaTitle = $metaTitle;
        }
        // This hook is kept for backward compatibility, it was previously called by AdminController::setMedia which is not called any more.
        // To keep this hook that needs to be called as soon as possible in the rendering workflow, we execute in this
        // component's mount method as the component is always displayed and the mount method is executed early
        $this->hookDispatcher->dispatchWithParameters('actionAdminControllerSetMedia');
    }

    public function getEmployeeToken(): string
    {
        return Tools::getAdminToken('AdminEmployees');
    }

    public function getJsDef(): array
    {
        return array_merge(
            [
                'baseDir' => $this->context->getContext()->shop->getBaseURI(),
                'baseAdminDir' => $this->context->getContext()->shop->getBaseURI() . basename(_PS_ADMIN_DIR_) . '/',
                'currency' => [
                    'iso_code' => $this->context->getContext()->currency->iso_code,
                    'sign' => $this->context->getContext()->currency->symbol,
                    'name' => $this->context->getContext()->currency->name,
                    'format' => $this->context->getContext()->currency->format,
                ],
                'currency_specifications' => $this->preparePriceSpecifications(),
                'number_specifications' => $this->prepareNumberSpecifications(),
                'prestashop' => [
                    'debug' => $this->templateVariables->isDebugMode(),
                ],
                'show_new_orders' => $this->configuration->get('PS_SHOW_NEW_ORDERS'),
                'show_new_customers' => $this->configuration->get('PS_SHOW_NEW_CUSTOMERS'),
                'show_new_messages' => $this->configuration->get('PS_SHOW_NEW_MESSAGES'),
            ],
            Media::getJsDef(),
        );
    }

    public function getPsVersion(): string
    {
        return $this->templateVariables->getVersion();
    }

    public function getIsoUser(): string
    {
        return $this->templateVariables->getIsoUser();
    }

    public function getCountryIsoCode(): string
    {
        return $this->countryContext->getIsoCode();
    }

    public function getLangIsRtl(): bool
    {
        return $this->templateVariables->isRtlLanguage();
    }

    public function getShopName(): string
    {
        return $this->configuration->get('PS_SHOP_NAME');
    }

    public function getControllerName(): string
    {
        return $this->legacyControllerContext->controller_name;
    }

    public function getImgDir(): string
    {
        return $this->shopContext->getBaseURI() . 'img/';
    }

    public function getFullLanguageCode(): string
    {
        return $this->languageContext->getLanguageCode();
    }

    public function getFullCldrLanguageCode(): string
    {
        return $this->languageContext->getLanguageCode();
    }

    public function getRoundMode(): int
    {
        return (int) $this->configuration->get('PS_PRICE_ROUND_MODE');
    }

    public function getLegacyToken(): string
    {
        return $this->legacyControllerContext->token;
    }

    public function getDefaultLanguage(): int
    {
        return $this->defaultLanguageContext->getId();
    }

    public function getCurrentIndex(): string
    {
        return $this->legacyControllerContext->currentIndex;
    }

    public function getEditForLabel(): string
    {
        if (Shop::getContext() === Shop::CONTEXT_SHOP) {
            return $this->translator->trans('This field will be modified for this shop:', [], 'Admin.Notifications.Info')
                . sprintf('<b>%s</b>', $this->getShopName());
        } elseif (Shop::getContext() === Shop::CONTEXT_GROUP) {
            return $this->translator->trans('This field will be modified for all shops in this shop group:', [], 'Admin.Notifications.Info')
                . sprintf('<b>%s</b>', $this->getShopName());
        }

        return $this->translator->trans('This field will be modified for all your shops.', [], 'Admin.Notifications.Info');
    }

    public function getCssFiles(): array
    {
        return $this->legacyControllerContext->css_files;
    }

    public function getJsFiles(): array
    {
        return $this->legacyControllerContext->js_files;
    }

    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    /**
     * Prepare price specifications to display cldr prices in javascript context.
     */
    private function preparePriceSpecifications(): array
    {
        /** @var Context $context */
        $context = $this->context->getContext();
        /* @var Currency */
        $currency = $context->currency;
        /* @var PriceSpecification */
        $priceSpecification = $this->languageContext->getPriceSpecification($currency->iso_code);

        return array_merge(
            ['symbol' => $priceSpecification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
            $priceSpecification->toArray()
        );
    }

    /**
     * Prepare number specifications to display cldr numbers in javascript context.
     */
    private function prepareNumberSpecifications(): array
    {
        /** @var Context $context */
        $context = $this->context->getContext();
        /* @var NumberSpecification */
        $numberSpecification = $this->languageContext->getNumberSpecification();

        return array_merge(
            ['symbol' => $numberSpecification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
            $numberSpecification->toArray()
        );
    }
}
