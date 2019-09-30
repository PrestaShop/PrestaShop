<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\TaxRulesGroup;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueTaxRuleBehavior;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ZipCodeRange;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRuleConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\EventSubscriber\TaxRuleFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for tax rule add/edit
 */
class TaxRuleType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $stateChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $behaviorChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countriesChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $taxesChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     * @param FormChoiceProviderInterface $behaviorChoiceProvider
     * @param FormChoiceProviderInterface $countriesChoiceProvider
     * @param FormChoiceProviderInterface $taxesChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider,
        FormChoiceProviderInterface $behaviorChoiceProvider,
        FormChoiceProviderInterface $countriesChoiceProvider,
        FormChoiceProviderInterface $taxesChoiceProvider
    ) {
        $this->translator = $translator;
        $this->stateChoiceProvider = $stateChoiceProvider;
        $this->behaviorChoiceProvider = $behaviorChoiceProvider;
        $this->countriesChoiceProvider = $countriesChoiceProvider;
        $this->taxesChoiceProvider = $taxesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country_id', ChoiceType::class, [
                'choices' => array_merge(
                    [$this->translator->trans('All', [], 'Admin.Global') => CountryId::ALL_COUNTRIES_ID],
                    array_map('intval', $this->countriesChoiceProvider->getChoices())
                ),
                'required' => false,
                'placeholder' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('zip_code', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => TaxRuleConstraint::MAX_ZIP_CODE_RANGE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => TaxRuleConstraint::MAX_ZIP_CODE_RANGE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('behavior_id', ChoiceType::class, [
                'choices' => $this->behaviorChoiceProvider->getChoices(),
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('tax_id', ChoiceType::class, [
                'choices' => array_merge(
                    [$this->translator->trans('No tax', [], 'Admin.Catalog.Feature') => TaxId::NO_TAX_ID],
                    $this->taxesChoiceProvider->getChoices()
                ),
                'placeholder' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => TaxRuleConstraint::MAX_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => TaxRuleConstraint::MAX_DESCRIPTION_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                ],
            ]);

        $builder->addEventSubscriber(new TaxRuleFormSubscriber($this->translator, $this->stateChoiceProvider));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'constraints' => [
                    new ZipCodeRange(),
                    new UniqueTaxRuleBehavior(),
                ],
            ]
        );
    }
}
