<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Exception;

/**
 * This class is used by Twig_Environment and provide layout methods callable from a twig template.
 */
class LayoutExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Constructor.
     *
     * Keeps the Context to look inside language settings.
     *
     * @param LegacyContext $context
     * @param string environment
     */
    public function __construct(LegacyContext $context, $environment)
    {
        $this->context = $context;
        $this->environment = $environment;
        $this->configuration = new Configuration();
    }

    /**
     * Provides globals for Twig templates.
     *
     * @return array The base globals available in twig templates.
     */
    public function getGlobals()
    {
        return array(
            'default_currency' => $this->context->getEmployeeCurrency(),
            'root_url' => $this->context->getRootUrl(),
            'js_translatable' => array(),
        );
    }

    /**
     * Define available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('configuration', array($this, 'getConfiguration')),
        );
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getLegacyLayout', array($this, 'getLegacyLayout')),
            new \Twig_SimpleFunction('getAdminLink', array($this, 'getAdminLink')),
            new \Twig_SimpleFunction('youtube_link', array($this, 'getYoutubeLink')),
        );
    }

    /**
     * Returns a legacy configuration key.
     *
     * @param string $key
     *
     * @return array An array of functions
     */
    public function getConfiguration($key)
    {
        return $this->configuration->get($key);
    }

    /**
     * Get admin legacy layout into old controller context.
     *
     * Parameters can be set manually into twig template or sent from controller
     * For details : check Resources/views/Admin/Layout.html.twig
     *
     * @param string        $controllerName    The legacy controller name
     * @param string        $title             The page title to override default one
     * @param array         $headerToolbarBtn  The header toolbar to override
     * @param string        $displayType       The legacy display type variable
     * @param bool          $showContentHeader Can force header toolbar (buttons and title) to be hidden with false value
     * @param array|string  $headerTabContent  Tabs labels
     * @param bool          $enableSidebar     Allow to use right sidebar to display docs for instance
     * @param string        $helpLink          If specified, will be used instead of legacy one
     *
     * @throws Exception if legacy layout has no $content var replacement
     *
     * @return string The html layout
     */
    public function getLegacyLayout(
        $controllerName = '',
        $title = '',
        $headerToolbarBtn = array(),
        $displayType = '',
        $showContentHeader = true,
        $headerTabContent = '',
        $enableSidebar = false,
        $helpLink = '',
        $routeName = ''
    ) {
        if ($this->environment == 'test') {
            return <<<EOF
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
            $routeName
        );

        //test if legacy template from "content.tpl" has '{$content}'
        if (false === strpos($layout, '{$content}')) {
            throw new Exception('PrestaShopBundle\Twig\LayoutExtension cannot find the {$content} string in legacy layout template', 1);
        }

        $content = str_replace(
            array(
                '{$content}',
                'var currentIndex = \'index.php\';',
                '</head>',
                '</body>',
            ),
            array(
                '{% block content_header %}{% endblock %}
                 {% block content %}{% endblock %}
                 {% block content_footer %}{% endblock %}
                 {% block sidebar_right %}{% endblock %}',
                'var currentIndex = \''.$this->context->getAdminLink($controllerName).'\';',
                '{% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}</head>',
                '{% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}</body>',
            ),
            $layout
        );

        return $content;
    }

    /**
     * This is a Twig port of the Smarty {$link->getAdminLink()} function.
     *
     * @param string        $controller  the controller name
     * @param bool          $withToken
     * @param array[string] $extraParams
     *
     * @return string
     */
    public function getAdminLink($controllerName, $withToken = true, $extraParams = array())
    {
        return $this->context->getAdminLink($controllerName, $withToken, $extraParams);
    }

    /**
     * KISS function to get an embeded iframe from Youtube.
     */
    public function getYoutubeLink($watchUrl)
    {
        $embedUrl = str_replace(array('watch?v=', 'youtu.be/'), array('embed/', 'youtube.com/embed/'), $watchUrl);

        return '<iframe width="560" height="315" src="'.$embedUrl.
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
