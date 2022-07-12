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

namespace PrestaShopBundle\Form\Admin\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form extension introduce a form_theme option that can be used on any form (usually it's more
 * on compound forms), this allows setting the form theme in the form type options instead of the twig
 * template. Thus adding this option on a form:
 *
 *     [
 *         'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/features.html.twig',
 *     ]
 *
 * is equivalent to doing this in the twig template:
 *
 *     {% form_theme form '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/features.html.twig' %}
 *
 * This option is very handy to customize collection sub-types (or any other container form type), or if
 * a module wishes to add its custom form type that needs a custom type. Since the rendering of the recent
 * form is automatic with a single form_row it is hard to define the form theme from twig that's one of the
 * use cases of this extension.
 *
 * An addition option exist with this extension use_default_themes which is the equivalent of the only keyword
 * used in twig:
 *
 *     {% form_theme productForm with ['@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product.html.twig'] only %}
 *
 * It is true by default, meaning to get the same behaviour as the twig keyword only you need to specify use_default_themes
 * to false.
 */
class FormThemeExtension extends AbstractTypeExtension
{
    /**
     * @var FormRendererInterface
     */
    protected $formRenderer;

    /**
     * @param FormRendererInterface $formRenderer
     */
    public function __construct(FormRendererInterface $formRenderer)
    {
        $this->formRenderer = $formRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // Define the form theme template path for the form
                'form_theme' => null,
                // Specify if default theme should be used as well
                'use_default_themes' => true,
            ])
            ->setAllowedTypes('form_theme', ['null', 'string', 'array'])
            ->setAllowedTypes('use_default_themes', 'bool')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['form_theme'])) {
            $formThemes = is_array($options['form_theme']) ? $options['form_theme'] : [$options['form_theme']];
            $this->formRenderer->setTheme($view, $formThemes, $options['use_default_themes']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
