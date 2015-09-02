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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Views;

class Twig extends \PrestaShop\PrestaShop\View
{
    public $parserDirectory = null;

    public $twigTemplateDirs = array();

    public $parserOptions = array();

    public $parserExtensions = array();

    private $parserInstance = null;

    /**
     * Render Twig Template
     *
     * @param string $template
     * @param null $data
     * @return string
     */
    public function render($template, $data = null)
    {
        $env = $this->getInstance();
        $parser = $env->loadTemplate($template);

        $data = array_merge($this->all(), (array) $data);

        return $parser->render($data);
    }

    /**
     * Creates new TwigEnvironment
     *
     * @return \Twig_Environment
     */
    public function getInstance()
    {
        if (!$this->parserInstance) {
            if (!class_exists('\Twig_Autoloader')) {
                require_once $this->parserDirectory . '/Autoloader.php';
            }

            $this->parserOptions = array(
                'debug' => true,
                'cache' => $this->parserCacheDirectory
            );

            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem($this->getTemplateDirs());
            $this->parserInstance = new \Twig_Environment(
                $loader,
                $this->parserOptions
            );

            foreach ($this->parserExtensions as $ext) {
                $extension = is_object($ext) ? $ext : new $ext;
                $this->parserInstance->addExtension($extension);
            }
        }

        return $this->parserInstance;
    }

    /**
     * Get a list of template directories
     *
     * Returns an array of templates
     *
     * @return array
     **/
    private function getTemplateDirs()
    {
        return _PS_BO_ALL_THEMES_DIR_ . 'default/template';
    }
}
