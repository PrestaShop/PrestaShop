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
use PrestaShop\PrestaShop\Core\Link\LinkInterface;
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
     * @param string $environment
     * @param ShopConfigurationInterface $configuration
     * @param CurrencyDataProvider $currencyDataProvider
     */
    public function __construct(
        private readonly LegacyContext $context,
        private readonly LinkInterface $link,
        private readonly string $environment,
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
        } catch (\Exception $e) {
            $defaultCurrency = null;
        }
        try {
            $rootUrl = $this->context->getRootUrl();
        } catch (\Exception $e) {
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
            new TwigFunction('getLegacyLayout', [$this, 'getLegacyLayout']),
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
    public function getConfiguration($key, $default = null, ShopConstraint $shopConstraint = null)
    {
        return $this->configuration->get($key, $default, $shopConstraint);
    }

    /**
     * Get admin legacy layout into old controller context.
     *
     * Parameters can be set manually into twig template or sent from controller
     * For details : check Resources/views/Admin/Layout.html.twig
     *
     * @param string $controllerName The legacy controller name
     * @param string $title The page title to override default one
     * @param array $headerToolbarBtn The header toolbar to override
     * @param string $displayType The legacy display type variable
     * @param bool $showContentHeader Can force header toolbar (buttons and title) to be hidden with false value
     * @param array|string $headerTabContent Tabs labels
     * @param bool $enableSidebar Allow to use right sidebar to display docs for instance
     * @param string $helpLink If specified, will be used instead of legacy one
     * @param string[] $jsRouterMetadata JS Router needed configuration settings: base_url and security token
     * @param string $metaTitle
     * @param bool $useRegularH1Structure allows complex <h1> structure if set to false
     *
     * @throws Exception if legacy layout has no $content var replacement
     *
     * @return string The html layout
     */
    public function getLegacyLayout(
        $controllerName = '',
        $title = '',
        $headerToolbarBtn = [],
        $displayType = '',
        $showContentHeader = true,
        $headerTabContent = '',
        $enableSidebar = false,
        $helpLink = '',
        $jsRouterMetadata = [],
        $metaTitle = '',
        $useRegularH1Structure = true,
        $baseLayout = 'layout.tpl'
    ) {
        if ($this->environment == 'test') {
            return <<<'EOF'
<html>
  <head>
    <title>Test layout</title>
    {% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}
  </head>
  <body>
    {% block content_header %}{% endblock %}
    {% block content %}{% endblock %}
    {% block content_footer %}{% endblock %}
    {% block javascripts %}{% endblock %}
    {% block extra_javascripts %}{% endblock %}
    {% block translate_javascripts %}{% endblock %}
  </body>
</html>
EOF;
        }

        $layout = $this->context->getLegacyLayout(
            $controllerName,
            $title,
            $headerToolbarBtn,
            $displayType,
            $showContentHeader,
            $headerTabContent,
            $enableSidebar,
            $helpLink,
            $jsRouterMetadata,
            $metaTitle,
            $useRegularH1Structure,
            $baseLayout
        );

        // There is nothing to display no legacy layout are generated
        if ($layout === '') {
            return '';
        }

        // Test if legacy template from "content.tpl" has '{$content}'
        if (!str_contains($layout, '{$content}')) {
            throw new Exception('PrestaShopBundle\Twig\LayoutExtension cannot find the {$content} string in legacy layout template', 1);
        }

        $explodedLayout = explode('{$content}', $layout);
        $header = explode('</head>', $explodedLayout[0]);
        $footer = explode('</body>', $explodedLayout[1]);

        return $this->escapeSmarty(str_replace('var currentIndex = \'index.php\';', 'var currentIndex = \'' . $this->link->getAdminLink($controllerName) . '\';', $header[0]))
            . '{% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}</head>'
            . $this->escapeSmarty($header[1])
            . '{% block content_header %}{% endblock %}'
            . '{% block content %}{% endblock %}'
            . '{% block content_footer %}{% endblock %}'
            . '{% block sidebar_right %}{% endblock %}'
            . $this->escapeSmarty($footer[0])
            . '{% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}</body>'
            . $this->escapeSmarty($footer[1]);
    }

    private function escapeSmarty(string $template): string
    {
        // Hard limit of twig filter at 8191 characters (2^13 - 1)
        // Split the string in multiple chunks
        $strings = str_split($template, 2000);
        $return = '';
        foreach ($strings as $string) {
            $return .= '{{ \'' . addcslashes($string, "\\'\0") . '\' | raw }}';
        }

        return $return;
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
        return $this->link->getAdminLink($controllerName, $withToken, $extraParams);
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
