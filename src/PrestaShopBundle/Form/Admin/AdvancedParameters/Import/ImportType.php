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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use function foo\func;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //@todo: improve
        $locales = [];
        foreach ($this->locales as $locale) {
            $locales[$locale['name']] = $locale['iso_code'];
        }

        $builder
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
            ->add('file', FileType::class)
            ->add('iso_lang', ChoiceType::class, [
                'choices' => $locales,
            ])
            ->add('separator', TextType::class, [
                'data' => ';',
            ])
            ->add('multiple_value_separator', TextType::class, [
                'data' => ',',
            ])
            ->add('truncate', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'choice_translation_domain' => 'Admin.Global',
            ])
            ->add('match_ref', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'choice_translation_domain' => 'Admin.Global',
            ])
            ->add('regenerate', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'choice_translation_domain' => 'Admin.Global',
            ])
            ->add('forceIDs', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'choice_translation_domain' => 'Admin.Global',
            ])
            ->add('sendemail', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'choice_translation_domain' => 'Admin.Global',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }
}