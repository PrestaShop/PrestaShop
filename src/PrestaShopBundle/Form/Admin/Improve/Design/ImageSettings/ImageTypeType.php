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

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Form type for image type add/edit.
 */
class ImageTypeType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('Letters, underscores and hyphens only (e.g. "small_custom", "cart_medium", "large", "thickbox_extra-large").', 'Admin.Design.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notification.Error',
                            [
                                '%s' => sprintf('"%s"', $this->trans('Name', 'Admin.Global')),
                            ]
                        ),
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z0-9_-]*$/',
                    ]),
                ],
            ])
            ->add('width', TextWithUnitType::class, [
                'label' => $this->trans('Width', 'Admin.Global'),
                'help' => $this->trans('Maximum image width in pixels.', 'Admin.Design.Help'),
                'unit' => $this->trans('pixels', 'Admin.Design.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notification.Error',
                            [
                                '%s' => sprintf('"%s"', $this->trans('Width', 'Admin.Global')),
                            ]
                        ),
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            'This field is invalid, it must contain numeric values',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{1,4}$/',
                    ]),
                ],
            ])
            ->add('height', TextWithUnitType::class, [
                'label' => $this->trans('Height', 'Admin.Global'),
                'help' => $this->trans('Maximum image height in pixels.', 'Admin.Design.Help'),
                'unit' => $this->trans('pixels', 'Admin.Design.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notification.Error',
                            [
                                '%s' => sprintf('"%s"', $this->trans('Height', 'Admin.Global')),
                            ]
                        ),
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            'This field is invalid, it must contain numeric values',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{1,4}$/',
                    ]),
                ],
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
            ]);
    }
}
