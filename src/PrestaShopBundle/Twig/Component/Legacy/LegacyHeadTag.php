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

namespace PrestaShopBundle\Twig\Component\Legacy;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Twig\Component\HeadTag;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\TemplateVariables;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/head_tag.html.twig')]
class LegacyHeadTag extends HeadTag
{
    use LegacyControllerTrait;

    public function __construct(
        Configuration $configuration,
        MenuBuilder $menuBuilder,
        TranslatorInterface $translator,
        HookDispatcherInterface $hookDispatcher,
        TemplateVariables $templateVariables,
        CountryContext $countryContext,
        ShopContext $shopContext,
        LanguageContext $languageContext,
        LanguageContext $defaultLanguageContext,
        CurrencyContext $currencyContext,
        LegacyControllerContext $legacyControllerContext,
        protected readonly LegacyContext $context,
        protected string $adminFolderName,
        protected string $psVersion,
        protected RequestStack $requestStack,
    ) {
        parent::__construct(
            $configuration,
            $menuBuilder,
            $translator,
            $hookDispatcher,
            $templateVariables,
            $countryContext,
            $shopContext,
            $languageContext,
            $defaultLanguageContext,
            $currencyContext,
            $legacyControllerContext
        );
    }

    public function mount(string $metaTitle = '', bool $loadLegacyMedia = false): void
    {
        if ($loadLegacyMedia) {
            $this->loadLegacyMedia();
        }
        parent::mount($this->hasLegacyController() ? $this->getLegacyMetaTitle() : $metaTitle);
    }

    public function getControllerName(): string
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()->controller_name;
        }

        return parent::getControllerName();
    }

    public function getLegacyToken(): string
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()->token;
        }

        return parent::getLegacyToken();
    }

    public function getCurrentIndex(): string
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()::$currentIndex;
        }

        return parent::getCurrentIndex();
    }

    public function getCssFiles(): array
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()->css_files;
        }

        return parent::getCssFiles();
    }

    public function getJsFiles(): array
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()->js_files;
        }

        return parent::getJsFiles();
    }

    /**
     * Legacy controller builds the meta title differently, so we match this for backward compatibility and so that the UI
     * tests can run with their expected values.
     *
     * @return string
     */
    protected function getLegacyMetaTitle(): string
    {
        $legacyMetaTitle = $this->getLegacyController()->getMetaTitle();
        if (empty($legacyMetaTitle) && !empty($this->getLegacyController()->getToolbarTitle())) {
            $legacyMetaTitle = $this->getLegacyController()->getToolbarTitle();
        }

        if (empty($legacyMetaTitle)) {
            $breadcrumbs = $this->menuBuilder->getBreadcrumbLinks();
            if (empty($breadcrumbs)) {
                return '';
            } else {
                return $breadcrumbs['tab']->name;
            }
        }

        if (is_array($legacyMetaTitle)) {
            $legacyMetaTitle = strip_tags(implode(' ' . $this->configuration->get('PS_NAVIGATION_PIPE') . ' ', $legacyMetaTitle));
        }

        return $legacyMetaTitle;
    }

    /**
     * This is an equivalent of AdminController::setMedia(false)
     *
     * @return void
     */
    protected function loadLegacyMedia(): void
    {
        $jsDir = rtrim($this->shopContext->getBaseURI(), '/') . '/js';
        $adminDir = rtrim($this->shopContext->getBaseURI(), '/') . '/' . $this->adminFolderName;
        if ($this->languageContext->isRTL()) {
            $this->addCSS($adminDir . '/themes/default/public/rtl.css?v=' . $this->psVersion, 'all', 0);
        }

        // Bootstrap
        $this->addCSS($adminDir . '/themes/default/css/theme.css?v=' . $this->psVersion, 'all', 0);
        $this->addCSS($adminDir . '/themes/default/css/vendor/titatoggle-min.css', 'all', 0);
        $this->addCSS($adminDir . '/themes/default/public/theme.css?v=' . $this->psVersion, 'all', 0);

        // add Jquery 3 and its migration script
        $this->addJs($jsDir . '/jquery/jquery-3.7.1.min.js');
        $this->addJs($jsDir . '/jquery/bo-migrate-mute.min.js');
        $this->addJs($jsDir . '/jquery/jquery-migrate-3.4.0.min.js');

        $this->addJqueryPlugin(['scrollTo', 'alerts', 'chosen', 'autosize', 'fancybox']);
        $this->addJqueryPlugin('growl', null, false);
        $this->addJqueryUI(['ui.slider', 'ui.datepicker']);

        $this->addJS($adminDir . '/themes/default/js/vendor/bootstrap.min.js');
        $this->addJS($adminDir . '/themes/default/js/vendor/modernizr.min.js');
        $this->addJS($adminDir . '/themes/default/js/modernizr-loads.js');
        $this->addJS($adminDir . '/themes/default/js/vendor/moment-with-langs.min.js');
        $this->addJS($adminDir . '/themes/default/public/theme.bundle.js?v=' . $this->psVersion);

        $this->addJS($jsDir . '/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

        if ((bool) $this->requestStack->getCurrentRequest() || !$this->requestStack->getCurrentRequest()->get('liteDisplaying')) {
            $this->addJS($adminDir . '/themes/default/js/help.js?v=' . $this->psVersion);
        }

        if (!$this->requestStack->getCurrentRequest() || !$this->requestStack->getCurrentRequest()->get('submitFormAjax')) {
            $this->addJS($jsDir . '/admin/notifications.js?v=' . $this->psVersion);
        }

        // Specific Admin Theme
        $this->addCSS($adminDir . '/themes/default/css/overrides.css', 'all', PHP_INT_MAX);

        $this->addCSS($adminDir . '/themes/new-theme/public/create_product_default_theme.css?v=' . $this->psVersion, 'all', 0);
        $this->addJS([
            $jsDir . '/admin.js?v=' . $this->psVersion, // TODO: SEE IF REMOVABLE
            $adminDir . '/themes/new-theme/public/cldr.bundle.js?v=' . $this->psVersion,
            $jsDir . '/tools.js?v=' . $this->psVersion,
            $adminDir . '/public/bundle.js?v=' . $this->psVersion,
        ]);

        // This is handled as an external common dependency for both themes, but once new-theme is the only one it should be integrated directly into the main.bundle.js file
        $this->addJS($adminDir . '/themes/new-theme/public/create_product.bundle.js?v=' . $this->psVersion);
    }

    protected function addCss(array|string $cssUri, string $cssMediaType = 'all', ?int $offset = null, bool $checkPath = true): void
    {
        if ($this->hasLegacyController()) {
            $this->getLegacyController()->addCSS($cssUri, $cssMediaType, $offset, $checkPath);
        } else {
            $this->legacyControllerContext->addCSS($cssUri, $cssMediaType, $offset, $checkPath);
        }
    }

    protected function addJs(array|string $jsUri, bool $checkPath = true): void
    {
        if ($this->hasLegacyController()) {
            $this->getLegacyController()->addJS($jsUri, $checkPath);
        } else {
            $this->legacyControllerContext->addJS($jsUri, $checkPath);
        }
    }

    protected function addJqueryUI(array|string $component, string $theme = 'base', bool $checkDependencies = true): void
    {
        if ($this->hasLegacyController()) {
            $this->getLegacyController()->addJqueryUI($component, $theme, $checkDependencies);
        } else {
            $this->legacyControllerContext->addJqueryUI($component, $theme, $checkDependencies);
        }
    }

    protected function addJqueryPlugin(array|string $name, ?string $folder = null, bool $css = true): void
    {
        if ($this->hasLegacyController()) {
            $this->getLegacyController()->addJqueryPlugin($name, $folder, $css);
        } else {
            $this->legacyControllerContext->addJqueryPlugin($name, $folder, $css);
        }
    }
}
