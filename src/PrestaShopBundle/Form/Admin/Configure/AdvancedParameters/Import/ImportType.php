<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Import" form in Import page.
 */
class ImportType extends TranslatorAwareType
{
    const DEFAULT_SEPARATOR = ';';
    const DEFAULT_MULTIVALUE_SEPARATOR = ',';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('csv', HiddenType::class)
            ->add('entity', ChoiceType::class, [
                'choices' => [
                    $this->trans('Categories', 'Admin.Global') => 0,
                    $this->trans('Products', 'Admin.Global') => 1,
                    $this->trans('Combinations', 'Admin.Global') => 2,
                    $this->trans('Customers', 'Admin.Global') => 3,
                    $this->trans('Addresses', 'Admin.Global') => 4,
                    $this->trans('Brands', 'Admin.Global') => 5,
                    $this->trans('Suppliers', 'Admin.Global') => 6,
                    $this->trans('Alias', 'Admin.Shopparameters.Feature') => 7,
                    $this->trans('Store contacts', 'Admin.Advparameters.Feature') => 8,
                ],
            ])
            ->add('file', FileType::class, [
                'required' => false,
            ])
            ->add('iso_lang', ChoiceType::class, [
                'choices' => $this->getLocaleChoices(),
            ])
            ->add('separator', TextType::class)
            ->add('multiple_value_separator', TextType::class)
            ->add('truncate', SwitchType::class, [
                'data' => false,
            ])
            ->add('match_ref', SwitchType::class, [
                'data' => false,
            ])
            ->add('regenerate', SwitchType::class, [
                'data' => false,
            ])
            ->add('forceIDs', SwitchType::class, [
                'data' => false,
            ])
            ->add('sendemail', SwitchType::class, [
                'data' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }
}
