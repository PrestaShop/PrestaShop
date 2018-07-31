<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to create a translatable form
 */
class TranslateType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     *
     * Builds form fields for each locales
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $i = 0;
        foreach ($options['locales'] as $locale) {
            $options['options']['empty_data'] = '';
            $locale_options = $options['options'];
            $locale_options['label'] = $locale['iso_code'];
            if ($i > 0) {
                $locale_options['required'] = false;
                unset($locale_options['constraints']);
            }

            $builder->add($locale['id_lang'], $options['type'], $locale_options);
            $i++;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add the var locales and defaultLocale to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['defaultLocale'] = $options['locales'][0];
        $view->vars['hideTabs'] = $options['hideTabs'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => null,
            'options' => [],
            'locales' => [],
            'hideTabs' => true,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'translatefields';
    }
}
