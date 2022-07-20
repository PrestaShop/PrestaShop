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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class PackedProductType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unique_identifier', HiddenType::class, [
                'label' => false,
            ])
            ->add('product_id', HiddenType::class, [
                'label' => false,
            ])
            ->add('image', ImagePreviewType::class, [
                'label' => false,
            ])
            ->add('combination_id', HiddenType::class, [
                'label' => false,
            ])
            ->add('name', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('reference', TextPreviewType::class, [
                'label' => false,
                'preview_class' => 'reference-preview',
            ])
            // @Todo must be an IntegerType or NumberType, but according with a prototype EntitySearchInputType limitation, set to TextType
            // @link https://github.com/PrestaShop/PrestaShop/issues/28513
            ->add('quantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'js-comma-transformer',
                ],
                'constraints' => [
                    new NotBlank([]),
                    new Type([
                        'type' => 'numeric',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                ],
            ])
        ;
    }
}
