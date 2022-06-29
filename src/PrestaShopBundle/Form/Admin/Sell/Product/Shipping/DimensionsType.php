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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shipping;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class DimensionsType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $dimensionUnit;

    /**
     * @var string
     */
    private $weightUnit;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $dimensionUnit
     * @param string $weightUnit
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $dimensionUnit,
        string $weightUnit
    ) {
        parent::__construct($translator, $locales);
        $this->dimensionUnit = $dimensionUnit;
        $this->weightUnit = $weightUnit;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Width', 'Admin.Catalog.Feature'),
                'unit' => $this->dimensionUnit,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0,
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Height', 'Admin.Catalog.Feature'),
                'unit' => $this->dimensionUnit,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0,
            ])
            ->add('depth', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Depth', 'Admin.Catalog.Feature'),
                'unit' => $this->dimensionUnit,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0,
            ])
            ->add('weight', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Weight', 'Admin.Catalog.Feature'),
                'unit' => $this->weightUnit,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Package dimension', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'label_subtitle' => $this->trans('Adjust your shipping costs by filling in the product dimensions.', 'Admin.Catalog.Feature'),
            'required' => false,
            'columns_number' => 6,
        ]);
    }
}
