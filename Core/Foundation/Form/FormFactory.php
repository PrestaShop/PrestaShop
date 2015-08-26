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

namespace PrestaShop\PrestaShop\Form;

use Symfony\Component\Validator\Validation;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\Translator;
use PrestaShop\PrestaShop\Twig\Extension\TranslationExtension as TwigTranslationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

class FormFactory
{
    public $engine;
    public $builder;
    public $form_builder;

    public function __construct()
    {
        // Set up the CSRF provider
        $csrfProvider = new DefaultCsrfProvider(_COOKIE_KEY_);

        // Set up template engine
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(
                array(_PS_BO_ALL_THEMES_DIR_.'default/template', _PS_BO_ALL_THEMES_DIR_ . 'default/template/Form/Twig')
            )
        );

        $formEngine = new TwigRendererEngine(array('bootstrap_3_layout.html.twig'));
        $formEngine->setEnvironment($twig);

        $twig->addExtension(new TwigTranslationExtension(new Translator('')));
        $twig->addExtension(new FormExtension(new TwigRenderer($formEngine, $csrfProvider)));

        $this->engine = $twig;

        // Set up the Form component
        $this->builder = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new CsrfExtension($csrfProvider))
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory()
            ->createBuilder();
    }

    public function create()
    {
        return $this->builder;
    }

    public function render($template, $params)
    {
        return $this->engine->render($template, $params);
    }
}
