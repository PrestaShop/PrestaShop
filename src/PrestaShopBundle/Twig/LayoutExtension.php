<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpKernel\Kernel;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * This class is used by Twig_Environment and provide layout methods callable from a twig template
 */
class LayoutExtension extends \Twig_Extension
{
    private $context;
    private $environment;
    private $contextLegacy;

    /**
     * Constructor.
     *
     * Keeps the Context to look inside language settings.
     *
     * @param LegacyContext $context
     */
    public function __construct(LegacyContext $context, Kernel $kernel)
    {
        $this->context = $context;
        $this->environment = $kernel->getEnvironment();
        $this->configurationAdapter = new Configuration();
    }

    public function getGlobals()
    {
        return array(
            "root_url" => $this->context->getRootUrl(),
            "js_translatable" => [],
            "ps_configuration" => [
                "weight_unit" => $this->configurationAdapter->get('PS_WEIGHT_UNIT'),
            ]
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
        );
    }

    /**
     * Get admin legacy layout into old controller context
     *
     * Parameters can be set manually into twig tempalte or sent from controller
     * For details : check Resources/views/Admin/Layout.html.twig
     *
     * @param string $controllerName The legacy controller name
     * @param string $title The page title to override default one
     * @param array $headerToolbarBtn The header toolbar to override
     * @param string $displayType The legacy display type variable
     *
     * @return string The html layout
     */
    public function getLegacyLayout($controllerName = "", $title = "", $headerToolbarBtn = [], $displayType = "")
    {
        if ($this->environment == 'test') {
            return <<<EOF
<html>
  <head>
    <title>Test layout</title>
    {% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}
  </head>
  <body>
    {% block content_header %}{% endblock %}{% block content %}{% endblock %}{% block content_footer %}{% endblock %}
    {% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}
  </body>
</html>
EOF;
        }
        $content = str_replace(
            array(
                '{$content}',
                'var currentIndex = \'index.php\';',
                '</head>',
                '</body>',
            ),
            array(
                '{% block content_header %}{% endblock %}{% block content %}{% endblock %}{% block content_footer %}{% endblock %}',
                'var currentIndex = \''.$this->context->getAdminLink($controllerName).'\';',
                '{% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}</head>',
                '{% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}</body>',
            ),
            $this->context->getLegacyLayout($controllerName, $title, $headerToolbarBtn, $displayType)
        );

        return $content;
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
