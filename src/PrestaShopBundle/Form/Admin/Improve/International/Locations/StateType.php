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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueStateIsoCode;
use PrestaShop\PrestaShop\Core\Domain\State\StateSettings;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\ConfigurableCountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\ZoneChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class StateType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $countriesChoiceProvider;
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $zonesChoiceProvider;

    /**
     * StateType constructor.
     *
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $countriesChoiceProvider
     * @param ConfigurableFormChoiceProviderInterface $zonesChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $countriesChoiceProvider,
        ConfigurableFormChoiceProviderInterface $zonesChoiceProvider
    ) {
        $this->translator = $translator;
        $this->countriesChoiceProvider = $countriesChoiceProvider;
        $this->zonesChoiceProvider = $zonesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stateIdValue = isset($builder->getData()['id_state']) && $builder->getData()['id_state'] instanceof StateId ?
            $builder->getData()['id_state']->getValue() :
            null;

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'help' => $this->translator->trans(
                    'Provide the state name to be displayed in addresses and on invoices.',
                    [],
                    'Admin.International.Help'
                ),
                'label' => $this->translator->trans('Name', [], 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => StateSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters.',
                            ['%limit%' => 64],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                ],
            ])
            ->add('iso_code', TextType::class, [
                'label' => $this->translator->trans('ISO code', [], 'Admin.International.Feature'),
                'help' => $this->translator->trans('1 to 4 letter ISO code.', [], 'Admin.International.Help')
                    . ' '
                    . $this->translator->trans('You can prefix it with the country ISO code if needed.', [], 'Admin.International.Help'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        [
                            'max' => StateSettings::MAX_ISO_CODE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters.',
                                ['%limit%' => StateSettings::MAX_ISO_CODE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new TypedRegex([
                        'type' => 'state_iso_code',
                    ]),
                    new CleanHtml(),
                    new UniqueStateIsoCode([
                        'excludeStateId' => $stateIdValue,
                    ]),
                ],
                'attr' => [
                    'maxlength' => StateSettings::MAX_ISO_CODE_LENGTH,
                ],
            ])
            ->add('id_country', ConfigurableCountryChoiceType::class, [
                'label' => $this->translator->trans('Country', [], 'Admin.Global'),
                'help' => $this->translator->trans('Country where the state is located.', [], 'Admin.International.Help')
                    . ' '
                    . $this->translator->trans('Only the countries with the option "contains states" enabled are displayed.', [], 'Admin.International.Help'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'placeholder' => false,
                'contains_states' => true,
                'choices' => $this->countriesChoiceProvider->getChoices([
                    'contains_states' => true,
                ]),
            ])
            ->add('id_zone', ZoneChoiceType::class, [
                'label' => $this->translator->trans('Zone', [], 'Admin.Global'),
                'help' => $this->translator->trans('Geographical region where this state is located.', [], 'Admin.International.Help')
                    . ' '
                    . $this->translator->trans('Used for shipping', [], 'Admin.International.Help'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'placeholder' => false,
                'choices' => $this->zonesChoiceProvider->getChoices([]),
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->translator->trans('Status', [], 'Admin.Global'),
                'required' => true,
            ]);
    }
}
