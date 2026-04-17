<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class UploadQuotaType extends TranslatorAwareType
{
    public const FIELD_MAX_SIZE_ATTACHED_FILES = 'max_size_attached_files';
    public const FIELD_MAX_SIZE_DOWNLOADABLE_FILE = 'max_size_downloadable_product';
    public const FIELD_MAX_SIZE_PRODUCT_IMAGE = 'max_size_product_image';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    private UploadSizeConfigurationInterface $uploadSizeConfiguration;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurationInterface $configuration,
        UploadSizeConfigurationInterface $uploadSizeConfiguration
    ) {
        parent::__construct($translator, $locales);
        $this->configuration = $configuration;
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->configuration;
        $builder
            ->add(
                self::FIELD_MAX_SIZE_ATTACHED_FILES,
                IntegerType::class,
                [
                    'label' => $this->trans(
                        'Maximum size for attached files',
                        'Admin.Advparameters.Feature'
                    ),
                    'help' => $this->trans(
                        'Set the maximum size allowed for attachment files (in megabytes). This value has to be lower than or equal to the maximum file upload allotted by your server (currently: %size% MB).',
                        'Admin.Advparameters.Help',
                        [
                            '%size%' => round($this->uploadSizeConfiguration->getMaxUploadSizeInBytes() / 1048576),
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
                IntegerType::class,
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
                IntegerType::class,
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
