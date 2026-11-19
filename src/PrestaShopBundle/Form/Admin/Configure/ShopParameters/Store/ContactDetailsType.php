<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface as FormFormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactDetailsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        private readonly UrlGeneratorInterface $router,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        $countryId = !empty($data['id_country']) ? (int) $data['id_country'] : 0;

        $builder
            ->add('name', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_NAME',
                'label' => $this->trans('Shop name', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Displayed in emails and page titles.',
                    'Admin.Shopparameters.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Shop name', 'Admin.Shopparameters.Feature'))]
                        ),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'multistore_configuration_key' => 'PS_SHOP_EMAIL',
                'label' => $this->trans('Shop email address', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Displayed in emails sent to customers.',
                    'Admin.Shopparameters.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Shop email address', 'Admin.Shopparameters.Feature'))]
                        ),
                    ]),
                    new Email([
                        'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('registration_number', TextareaType::class, [
                'multistore_configuration_key' => 'PS_SHOP_DETAILS',
                'label' => $this->trans('Registration number', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Shop\'s registration information (e.g. SIRET or RCS).',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            ])
            // Legacy applied isAddress() to address1/address2 and isGenericName() to
            // postcode/city/phone/fax, with no length limit: these options are stored in
            // ps_configuration, not in the ps_store columns. See AdminStoresController::getConfigFieldsShop().
            ->add('address1', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_ADDR1',
                'label' => $this->trans('Shop address line 1', 'Admin.Shopparameters.Feature'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_ADDRESS])],
            ])
            ->add('address2', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_ADDR2',
                'label' => $this->trans('Shop address line 2', 'Admin.Shopparameters.Feature'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_ADDRESS])],
            ])
            ->add('postcode', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_CODE',
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            ])
            ->add('city', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_CITY',
                'label' => $this->trans('City', 'Admin.Global'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'multistore_configuration_key' => 'PS_SHOP_COUNTRY_ID',
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => false,
                'autocomplete' => true,
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                ],
            ]);

        // Added here so it keeps its position between Country and Phone, as legacy rendered it;
        // the listeners below only refresh it in place.
        $this->rebuildStateField($builder, $countryId);

        $builder
            ->add('phone', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_PHONE',
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            ])
            ->add('fax', TextType::class, [
                'multistore_configuration_key' => 'PS_SHOP_FAX',
                'label' => $this->trans('Fax', 'Admin.Global'),
                'required' => false,
                'constraints' => [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData() ?? [];
            $this->rebuildStateField($event->getForm(), (int) ($data['id_country'] ?? 0));
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData() ?? [];
            $form = $event->getForm();
            // A Country not overridden for the shop is disabled, so not submitted: the states are those of the inherited country
            $countryId = array_key_exists('id_country', $data)
                ? (int) $data['id_country']
                : (int) $form->get('id_country')->getData();
            $this->rebuildStateField($form, $countryId);
        });
    }

    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }

    /**
     * @param FormFormInterface|FormBuilderInterface $form
     */
    private function rebuildStateField($form, int $countryId): void
    {
        $stateChoices = $countryId > 0 ? $this->statesChoiceProvider->getChoices(['id_country' => $countryId]) : [];
        $options = [
            'label' => $this->trans('State', 'Admin.Global'),
            'required' => false,
            'choices' => $stateChoices,
            'row_attr' => [
                'class' => 'js-store-state-row',
            ],
            'autocomplete' => true,
            'attr' => [
                'visible' => !empty($stateChoices),
            ],
            'multistore_configuration_key' => 'PS_SHOP_STATE_ID',
        ];

        // Keep what the multistore extension set on the field (disabled when not overridden, shops dropdown),
        // the listeners below rebuild it after the extension ran.
        if ($form instanceof FormFormInterface && $form->has('id_state')) {
            $previousOptions = $form->get('id_state')->getConfig()->getOptions();
            if (isset($previousOptions['attr']['disabled'])) {
                $options['attr']['disabled'] = $previousOptions['attr']['disabled'];
            }
            if (isset($previousOptions['multistore_dropdown'])) {
                $options['multistore_dropdown'] = $previousOptions['multistore_dropdown'];
            }
        }

        $form->add('id_state', ChoiceType::class, $options);
    }
}
