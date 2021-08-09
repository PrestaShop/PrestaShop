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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

class UploadQuotaType extends TranslatorAwareType
{
    public const FIELD_MAX_SIZE_ATTACHED_FILES = 'max_size_attached_files';
    public const FIELD_MAX_SIZE_DOWNLOADABLE_FILE = 'max_size_downloadable_product';
    public const FIELD_MAX_SIZE_PRODUCT_IMAGE = 'max_size_product_image';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->getConfiguration();
        $builder
            ->add(
                self::FIELD_MAX_SIZE_ATTACHED_FILES,
                TextWithUnitType::class,
                [
                    'label' => $this->trans(
                        'Maximum size for attached files',
                        'Admin.Advparameters.Feature'
                    ),
                    'help' => $this->trans(
                        'Set the maximum size allowed for attachment files (in megabytes). This value has to be lower than or equal to the maximum file upload allotted by your server (currently: %size% MB).',
                        'Admin.Advparameters.Help',
                        [
                            '%size%' => $configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                        ]
                    ),
                    'unit' => $this->trans('megabytes', 'Admin.Advparameters.Feature'),
                    'constraints' => [
                        new Type(
                            [
                                'value' => 'numeric',
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                        new GreaterThanOrEqual(
                            [
                                'value' => 0,
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                self::FIELD_MAX_SIZE_DOWNLOADABLE_FILE,
                TextWithUnitType::class,
                [
                    'label' => $this->trans(
                        'Maximum size for a downloadable product',
                        'Admin.Advparameters.Feature'
                    ),
                    'help' => $this->trans(
                        'Define the upload limit for a downloadable product (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server (currently: %size% MB).',
                        'Admin.Advparameters.Help',
                        [
                            '%size%' => $configuration->get('PS_LIMIT_UPLOAD_FILE_VALUE'),
                        ]
                    ),
                    'unit' => $this->trans('megabytes', 'Admin.Advparameters.Feature'),
                    'constraints' => [
                        new Type(
                            [
                                'value' => 'numeric',
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                        new GreaterThanOrEqual(
                            [
                                'value' => 0,
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                self::FIELD_MAX_SIZE_PRODUCT_IMAGE,
                TextWithUnitType::class,
                [
                    'label' => $this->trans(
                        'Maximum size for a product\'s image',
                        'Admin.Advparameters.Feature'
                    ),
                    'help' => $this->trans(
                        'Define the upload limit for an image (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server (currently: %size% MB).',
                        'Admin.Advparameters.Help',
                        [
                            '%size%' => $configuration->get('PS_LIMIT_UPLOAD_IMAGE_VALUE'),
                        ]
                    ),
                    'constraints' => [
                        new Type(
                            [
                                'value' => 'numeric',
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                        new GreaterThanOrEqual(
                            [
                                'value' => 0,
                                'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                            ]
                        ),
                    ],
                    'unit' => $this->trans('megabytes', 'Admin.Advparameters.Feature'),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'administration_upload_quota_block';
    }
}
