<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactDetailsType extends TranslatorAwareType
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
        $builder
            ->add('name', TextType::class, [
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
                'label' => $this->trans('Registration number', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Shop\'s registration information (e.g. SIRET or RCS).',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('address1', TextType::class, [
                'label' => $this->trans('Shop address line 1', 'Admin.Shopparameters.Feature'),
                'required' => false,
            ])
            ->add('address2', TextType::class, [
                'label' => $this->trans('Shop address line 2', 'Admin.Shopparameters.Feature'),
                'required' => false,
            ])
            ->add('postcode', TextType::class, [
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'required' => false,
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                    'data-toggle' => 'select2',
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => [],
                'row_attr' => ['class' => 'js-store-state-row d-none'],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-country-id' => 0,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
            ])
            ->add('fax', TextType::class, [
                'label' => $this->trans('Fax', 'Admin.Global'),
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData() ?? [];
            $countryId = (int) ($data['id_country'] ?? 0);
            $stateChoices = $countryId > 0 ? $this->statesChoiceProvider->getChoices(['id_country' => $countryId]) : [];
            $event->getForm()->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => $stateChoices,
                'row_attr' => ['class' => 'js-store-state-row' . (empty($stateChoices) ? ' d-none' : '')],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-country-id' => $countryId,
                ],
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();
            $countryId = (int) ($data['id_country'] ?? 0);
            $stateChoices = $countryId > 0 ? $this->statesChoiceProvider->getChoices(['id_country' => $countryId]) : [];
            $form->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => $stateChoices,
                'row_attr' => ['class' => 'js-store-state-row' . (empty($stateChoices) ? ' d-none' : '')],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-country-id' => $countryId,
                ],
            ]);
        });
    }
}
