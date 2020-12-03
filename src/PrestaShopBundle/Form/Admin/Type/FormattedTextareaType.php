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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class enabling TinyMCE on a Textarea field.
 */
class FormattedTextareaType extends AbstractType
{
    /**
     * Max size of UTF-8 content in MySQL text column
     */
    const LIMIT_TEXT_UTF8 = 21844;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'autoload' => true, // Start automatically TinyMCE
            'limit' => self::LIMIT_TEXT_UTF8,
        ]);
        $resolver->setAllowedTypes('limit', 'int');
        $resolver->setAllowedTypes('autoload', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = '';
        }

        if (true === $options['autoload']) {
            $view->vars['attr']['class'] .= ' autoload_rte';
        }
        $view->vars['attr']['counter'] = $options['limit'];
        $view->vars['constraints'] = [
            new TinyMceMaxLength([
                'max' => $options['limit'],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}
