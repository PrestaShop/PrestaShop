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

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
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
        $uploadMaxSize = (int) str_replace('M', '', ini_get('upload_max_filesize'));
        $postMaxSize = (int) str_replace('M', '', ini_get('post_max_size'));
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
                    'constraints' => $this->getUploadMaxSizeConstraints($uploadMaxSize),
                    'multistore_configuration_key' => 'PS_ATTACHMENT_MAXIMUM_SIZE'
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
                    'constraints' => $this->getUploadMaxSizeConstraints($postMaxSize),
                    'multistore_configuration_key' => 'PS_LIMIT_UPLOAD_FILE_VALUE'
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
                    'constraints' => $this->getUploadMaxSizeConstraints($uploadMaxSize),
                    'unit' => $this->trans('megabytes', 'Admin.Advparameters.Feature'),
                    'multistore_configuration_key' => 'PS_LIMIT_UPLOAD_IMAGE_VALUE'
                ]
            );
    }

    /**
     * @return Constraint[]
     */
    private function getUploadMaxSizeConstraints(int $serverMaxSize): array
    {
        return [
            new Type(
                [
                    'type' => 'numeric',
                    'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                ]
            ),
            new GreaterThanOrEqual(
                [
                    'value' => 0,
                    'message' => $this->trans('The field is invalid. Please enter an integer greater than or equal to 0.', 'Admin.Notifications.Error'),
                ]
            ),
            new LessThanOrEqual(
                [
                    'value' => $serverMaxSize,
                    'message' => $this->trans('The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.', 'Admin.Notifications.Error'),
                ]
            )
        ];
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

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
