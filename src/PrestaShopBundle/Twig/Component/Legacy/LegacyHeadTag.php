<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use Symfony\Component\Routing\RouterInterface;
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
        RouterInterface $router,
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
            $legacyControllerContext,
            $router
        );
    }

    public function mount(string $metaTitle = ''): void
    {
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
