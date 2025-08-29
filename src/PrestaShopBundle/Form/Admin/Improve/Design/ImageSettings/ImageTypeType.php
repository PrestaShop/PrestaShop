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

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ImageTypeType extends TranslatorAwareType
{
    /** Minimum size for width and height in pixels of the image type */
    private const MIN_PX_SIZE = 1;

    /** Maximum size for width and height in pixels for the image type */
    private const MAX_PX_SIZE = 9999;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name for the image type', 'Admin.Design.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_IMAGE_TYPE_NAME,
                    ]),
                ],
                'help' => $this->trans('Letters, underscores and hyphens only (e.g. "small_custom", "cart_medium", "large", "thickbox_extra-large").', 'Admin.Design.Help'),
            ])
            ->add('width', IntegerType::class, [
                'label' => $this->trans('Width', 'Admin.Global'),
                'attr' => [
                    'min' => self::MIN_PX_SIZE,
                    'max' => self::MAX_PX_SIZE,
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => self::MIN_PX_SIZE,
                        'max' => self::MAX_PX_SIZE,
                        'notInRangeMessage' => $this->trans(
                            'This field must be between %min% and %max%',
                            'Admin.Notifications.Error',
                            ['%min%' => self::MIN_PX_SIZE, '%max%' => self::MAX_PX_SIZE]
                        ),
                    ]),
                ],
                'help' => $this->trans('Maximum image width in pixels.', 'Admin.Design.Help'),
            ])
            ->add('height', IntegerType::class, [
                'label' => $this->trans('Height', 'Admin.Global'),
                'attr' => [
                    'min' => self::MIN_PX_SIZE,
                    'max' => self::MAX_PX_SIZE,
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => self::MIN_PX_SIZE,
                        'max' => self::MAX_PX_SIZE,
                        'notInRangeMessage' => $this->trans(
                            'This field must be between %min% and %max%',
                            'Admin.Notifications.Error',
                            ['%min%' => self::MIN_PX_SIZE, '%max%' => self::MAX_PX_SIZE]
                        ),
                    ]),
                ],
                'help' => $this->trans('Maximum image height in pixels.', 'Admin.Design.Help'),
            ])
            ->add('products', SwitchType::class, [
                'label' => $this->trans('Products', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Product images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('categories', SwitchType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Category images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('manufacturers', SwitchType::class, [
                'label' => $this->trans('Brands', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Brand images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('suppliers', SwitchType::class, [
                'label' => $this->trans('Suppliers', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Supplier images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('stores', SwitchType::class, [
                'label' => $this->trans('Stores', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Store images.', 'Admin.Design.Help'),
                'required' => false,
            ])
        ;
    }
}
