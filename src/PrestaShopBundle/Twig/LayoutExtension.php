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

namespace PrestaShopBundle\Twig;

use Currency;
use Exception;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This class is used by Twig_Environment and provide layout methods callable from a twig template.
 */
class LayoutExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Constructor.
     *
     * Keeps the Context to look inside language settings.
     *
     * @param LegacyContext $context
     * @param ShopConfigurationInterface $configuration
     * @param CurrencyDataProvider $currencyDataProvider
     */
    public function __construct(
        private readonly LegacyContext $context,
        private readonly ShopConfigurationInterface $configuration,
        private readonly CurrencyDataProvider $currencyDataProvider
    ) {
    }

    /**
     * Provides globals for Twig templates.
     *
     * @return array the base globals available in twig templates
     */
    public function getGlobals(): array
    {
        /*
         * As this is a twig extension we need to be very resilient and prevent it from crashing
         * the environment, for example the command debug:twig should not fail because of this extension
         */

        try {
            $defaultCurrency = $this->context->getEmployeeCurrency() ?: $this->currencyDataProvider->getDefaultCurrency();
        } catch (Exception) {
            $defaultCurrency = null;
        }
        try {
            $rootUrl = $this->context->getRootUrl();
        } catch (Exception) {
            $rootUrl = null;
        }

        return [
            'theme' => $this->context->getContext()->shop->theme,
            'default_currency' => $defaultCurrency,
            'default_currency_symbol' => $defaultCurrency instanceof Currency ? $defaultCurrency->getSymbol() : null,
            'root_url' => $rootUrl,
            'js_translatable' => [],
            'rtl_suffix' => $this->context->getContext()->language->is_rtl ? '_rtl' : '',
        ];
    }

    /**
     * Define available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return [
            new TwigFilter('configuration', [$this, 'getConfiguration'], ['deprecated' => true]),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getAdminLink', [$this, 'getAdminLink']),
            new TwigFunction('youtube_link', [$this, 'getYoutubeLink']),
            new TwigFunction('configuration', [$this, 'getConfiguration']),
        ];
    }

    /**
     * Returns a legacy configuration key.
     *
     * @param string $key
     * @param mixed $default Default value is null
     * @param ShopConstraint $shopConstraint Default value is null
     *
     * @return mixed
     */
    public function getConfiguration($key, $default = null, ?ShopConstraint $shopConstraint = null)
    {
        return $this->configuration->get($key, $default, $shopConstraint);
    }

    /**
     * This is a Twig port of the Smarty {$link->getAdminLink()} function.
     *
     * @param string $controllerName
     * @param bool $withToken
     * @param array<string> $extraParams
     *
     * @return string
     */
    public function getAdminLink($controllerName, $withToken = true, $extraParams = [])
    {
        return $this->context->getAdminLink($controllerName, $withToken, $extraParams);
    }

    /**
     * KISS function to get an embedded iframe from Youtube.
     */
    public function getYoutubeLink($watchUrl)
    {
        $embedUrl = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'youtube.com/embed/'], $watchUrl);

        return '<iframe width="560" height="315" src="' . $embedUrl .
            '" frameborder="0" allowfullscreen class="youtube-iframe m-x-auto"></iframe>';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_layout_extension';
    }
}
