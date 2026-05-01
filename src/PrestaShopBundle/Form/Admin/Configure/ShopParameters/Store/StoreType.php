<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class StoreType extends TranslatorAwareType
{
    private const MAX_PHONE_LENGTH = 16;
    private const MAX_POSTCODE_LENGTH = 12;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        private readonly int $contextCountryId,
        private readonly bool $isMultistoreEnabled,
        private readonly UrlGeneratorInterface $router,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        $countryId = !empty($data['id_country']) ? (int) $data['id_country'] : $this->contextCountryId;

        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'constraints' => [new DefaultLanguage()],
                'options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'The %s field is required.',
                                'Admin.Notifications.Error',
                                [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('address1', TranslatableType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'constraints' => [new DefaultLanguage()],
                'options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'The %s field is required.',
                                'Admin.Notifications.Error',
                                [sprintf('"%s"', $this->trans('Address', 'Admin.Global'))]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('address2', TranslatableType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
            ])
            ->add('postcode', TextType::class, [
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => self::MAX_POSTCODE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => self::MAX_POSTCODE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('City', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                    'data-toggle' => 'select2',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Country', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => $stateChoices = $this->statesChoiceProvider->getChoices(['id_country' => $countryId]),
                'row_attr' => ['class' => 'js-store-state-row' . (empty($stateChoices) ? ' d-none' : '')],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-country-id' => $countryId,
                ],
            ])
            ->add('latitude', TextType::class, [
                'label' => $this->trans('Latitude', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Store coordinates (e.g. 45.265469 or -0.265469)', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Latitude', 'Admin.Shopparameters.Feature'))]
                        ),
                    ]),
                ],
            ])
            ->add('longitude', TextType::class, [
                'label' => $this->trans('Longitude', 'Admin.Shopparameters.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Longitude', 'Admin.Shopparameters.Feature'))]
                        ),
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => self::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => self::MAX_PHONE_LENGTH]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_PHONE_NUMBER]),
                ],
            ])
            ->add('fax', TextType::class, [
                'label' => $this->trans('Fax', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => self::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => self::MAX_PHONE_LENGTH]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_PHONE_NUMBER]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('note', TranslatableType::class, [
                'label' => $this->trans('Note', 'Admin.Shopparameters.Feature'),
                'type' => FormattedTextareaType::class,
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => $this->trans('Picture', 'Admin.Global'),
                'required' => false,
            ])
            ->add('hours', TranslatableType::class, [
                'label' => $this->trans('Hours', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Enter opening/closing hours for each day. Format: HH:MM | HH:MM (e.g. 09:00 | 18:00). Leave blank for closed.',
                    'Admin.Shopparameters.Help'
                ),
                'type' => StoreHoursType::class,
                'required' => false,
            ])
        ;

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'required' => false,
            ]);
        }
    }
}
