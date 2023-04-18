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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Title" page.
 */
class TitleType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'constraints' => [new DefaultLanguage()],
                'label' => $this->trans(
                    'Title',
                    'Admin.Global'
                ),
            ])
            ->add('gender_type', ChoiceType::class, [
                'label' => $this->trans(
                    'Gender',
                    'Admin.Global'
                ),
                'expanded' => true,
                'placeholder' => false,
                'choices' => [
                    $this->trans('Male', 'Admin.Shopparameters.Feature') => Gender::TYPE_MALE,
                    $this->trans('Female', 'Admin.Shopparameters.Feature') => Gender::TYPE_FEMALE,
                    $this->trans('Neutral', 'Admin.Shopparameters.Feature') => Gender::TYPE_OTHER,
                ],
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => $this->trans(
                    'Image',
                    'Admin.Global'
                ),
                'required' => false,
            ])
            ->add('img_width', IntegerType::class, [
                'label' => $this->trans(
                    'Image width',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Image width in pixels. Enter "0" to use the original size.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('img_height', IntegerType::class, [
                'label' => $this->trans(
                    'Image height',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Image height in pixels. Enter "0" to use the original size.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
        ;
    }
}
