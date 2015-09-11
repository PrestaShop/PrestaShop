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

namespace PrestaShop\PrestaShop\Core\Foundation\View\Views;

use PrestaShop\PrestaShop\Core\Business\Context;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Translation\Translator;
use PrestaShop\PrestaShop\Core\Foundation\Twig\Extension\TranslationExtension as TwigTranslationExtension;
use PrestaShop\PrestaShop\Core\Foundation\Twig\Extension\RoutingExtension as TwigRoutingExtension;
use Symfony\Component\Validator\Validation;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use PrestaShop\PrestaShop\Core\Foundation\View\View;

class Twig extends View
{
    public $parserDirectory = null;
    public $twigTemplateDirs = [];
    public $parserOptions = [];
    public $parserExtensions = [];
    private $parserInstance = null;

    /**
     * Render Template
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
     * Creates new TwigEnv
     *
     * @return \Twig_Environment
     */
    public function getInstance()
    {
        if (!$this->parserInstance) {
            if (!class_exists('\Twig_Autoloader')) {
                require_once $this->parserDirectory . '/Autoloader.php';
            }

            $context = Context::getInstance();

            $this->parserOptions = array(
                'debug' => true,
                'cache' => $this->parserCacheDirectory
            );

            // Set up the CSRF provider
            $this->csrfProvider = new DefaultCsrfProvider(_COOKIE_KEY_);

            \Twig_Autoloader::register();
            $loader = new \Twig_Loader_Filesystem($this->getTemplateDirs());
            $this->parserInstance = new \Twig_Environment(
                $loader,
                $this->parserOptions
            );

            $formEngine = new TwigRendererEngine(array('bootstrap_3_layout.html.twig'));
            $formEngine->setEnvironment($this->parserInstance);

            $this->parserInstance->addExtension(new \Twig_Extension_Debug());
            $this->parserInstance->addExtension(new TwigTranslationExtension(new Translator('')));
            $this->parserInstance->addExtension(new TwigRoutingExtension($context->get('routerInstance')));
            $this->parserInstance->addExtension(new FormExtension(new TwigRenderer($formEngine, $this->csrfProvider)));

            foreach ($this->parserExtensions as $ext) {
                $extension = is_object($ext) ? $ext : new $ext;
                $this->parserInstance->addExtension($extension);
            }
        }

        return $this->parserInstance;
    }

    /**
     * Get template directories
     *
     * @return array
     **/
    private function getTemplateDirs()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            return array(
                _PS_BO_ALL_THEMES_DIR_ . 'default/template',
                _PS_BO_ALL_THEMES_DIR_ . 'default/template/Core/Form/Twig'
            );
        } else {
            return array(
                _PS_THEME_DIR_
            );
        }
    }
}
