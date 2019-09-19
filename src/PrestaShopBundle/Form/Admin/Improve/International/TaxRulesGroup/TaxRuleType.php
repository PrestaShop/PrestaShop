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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRulesGroupConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\BehaviorChoiceType;
use PrestaShopBundle\Form\Admin\Type\CountryAndAllChoiceType;
use PrestaShopBundle\Form\Admin\Type\TaxChoiceType;
use PrestaShopBundle\Form\EventSubscriber\TaxRuleFormSubscriber;
use Symfony\Component\Form\AbstractType;
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
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider
    ) {
        $this->translator = $translator;
        $this->stateChoiceProvider = $stateChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', CountryAndAllChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('zipCode', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => TaxRulesGroupConstraint::MAX_TAX_RULE_ZIP_CODE_RANGE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => TaxRulesGroupConstraint::MAX_TAX_RULE_ZIP_CODE_RANGE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('behavior', BehaviorChoiceType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('tax', TaxChoiceType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => TaxRulesGroupConstraint::MAX_TAX_RULE_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => TaxRulesGroupConstraint::MAX_TAX_RULE_DESCRIPTION_LENGTH],
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
