<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Webservice;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;
use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Is used to create form for adding/editing Webservice Key
 */
class WebserviceKeyType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreFeatureUsed;

    /**
     * @var array
     */
    private $resourceChoices;

    /**
     * @var array
     */
    private $permissionChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreFeatureUsed
     * @param array $resourceChoices
     * @param array $permissionChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $isMultistoreFeatureUsed,
        array $resourceChoices,
        array $permissionChoices
    ) {
        parent::__construct($translator, $locales);
        $this->isMultistoreFeatureUsed = $isMultistoreFeatureUsed;
        $this->resourceChoices = $resourceChoices;
        $this->permissionChoices = $permissionChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', GeneratableTextType::class, [
                'label' => $this->trans('Key', 'Admin.Advparameters.Feature'),
                'help' => sprintf(
                    '%s<br>%s',
                    $this->trans('Webservice account key.', 'Admin.Advparameters.Feature'),
                    $this->trans(
                        'The key must be %length% characters long.',
                        'Admin.Notifications.Info',
                        [
                            '%length%' => Key::LENGTH,
                        ]
                    )
                ),
                'generated_value_length' => Key::LENGTH,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field is required.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'min' => Key::LENGTH,
                        'max' => Key::LENGTH,
                        'exactMessage' => $this->trans(
                            'Key length must be %length% characters long.',
                            'Admin.Advparameters.Notification',
                            [
                                '%length%' => Key::LENGTH,
                            ]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_WEBSERVICE_KEY,
                        'message' => $this->trans(
                            'Only non-accented characters, numbers, and the following special characters are allowed: %allowed_characters%',
                            'Admin.Advparameters.Notification',
                            [
                                '%allowed_characters%' => '@ ? # - _',
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Key description', 'Admin.Advparameters.Feature'),
                'help' => $this->trans(
                    'Quick description of the key: who it is for, what permissions it has, etc.',
                    'Admin.Advparameters.Help'
                ),
                'required' => false,
                'empty_data' => '',
            ])
            ->add('status', SwitchType::class, [
                'label' => $this->trans('Enable webservice key', 'Admin.Advparameters.Feature'),
                'required' => false,
            ])
            ->add('permissions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Permissions', 'Admin.Advparameters.Feature'),
                'table_label' => $this->trans('Resource', 'Admin.Global'),
                'required' => false,
                'choices' => $this->resourceChoices,
                'multiple_choices' => $this->getPermissionChoicesForResources(),
                'scrollable' => false,
                'headers_to_disable' => ['all'],
                'headers_fixed' => true,
            ])
        ;

        // remove "all" configuration since it's not an actual permission
        $builder->get('permissions')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                return $value;
            },
            function ($value) {
                if (isset($value['all'])) {
                    unset($value['all']);
                }

                return $value;
            }
        ));

        if ($this->isMultistoreFeatureUsed) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
            ]);

            $builder->get('shop_association')->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return null === $value ? [] : $value;
                },
                function ($value) {
                    return null === $value ? [] : $value;
                }
            ));
        }
    }

    /**
     * @return array
     */
    private function getPermissionChoicesForResources()
    {
        $choices = [];

        foreach ($this->permissionChoices as $name => $permission) {
            $choices[] = [
                'name' => $permission,
                'label' => $name,
                'multiple' => true,
                'choices' => $this->resourceChoices,
            ];
        }

        return $choices;
    }
}
