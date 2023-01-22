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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;
use PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer\SpecificPriceFixedPriceTransformer;
use PrestaShopBundle\Form\Admin\Type\PriceReductionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpecificPriceImpactType extends TranslatorAwareType
{
    // We need specific groups because the constraints are different when fields are disabled
    private const FIXED_PRICE_GROUP = 'fixed_price_group';
    private const REDUCTION_GROUP = 'reduction_group';

    /**
     * @var string
     */
    private $defaultCurrencyIso;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $defaultCurrencyIso
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrencyIso = $defaultCurrencyIso;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reduction', PriceReductionType::class, [
                'label' => $this->trans('Apply a discount to the initial price', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('For customers meeting the conditions, the initial price will be crossed out and the discount will be highlighted.', 'Admin.Catalog.Feature'),
                'required' => false,
                'constraints' => [
                    new Reduction([
                        'invalidPercentageValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. It must be greater than 0 and maximum %max%.',
                            'Admin.Notifications.Error',
                            ['%max%' => ReductionVO::MAX_ALLOWED_PERCENTAGE . '%']
                        ),
                        'invalidAmountValueMessage' => $this->trans(
                            'Reduction value "%value%" is invalid. It must be greater than 0.',
                            'Admin.Notifications.Error'
                        ),
                        'groups' => [self::REDUCTION_GROUP],
                    ]),
                ],
                'disabling_switch' => true,
                'disabled_value' => function ($data, FormInterface $form): bool {
                    return $this->shouldReductionBeDisabled($form);
                },
            ])
            ->add('fixed_price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Set specific price', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('Retail price (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'row_attr' => [
                    'class' => 'js-fixed-price-row',
                ],
                'currency' => $this->defaultCurrencyIso,
                'constraints' => [
                    new NotBlank(['groups' => [self::FIXED_PRICE_GROUP]]),
                    new Type(['type' => 'float', 'groups' => [self::FIXED_PRICE_GROUP]]),
                    new Positive(['groups' => [self::FIXED_PRICE_GROUP]]),
                ],
                'disabling_switch' => true,
                'disabled_value' => function ($data, FormInterface $form): bool {
                    return $this->shouldFixedPriceBeDisabled($form);
                },
            ])
        ;

        $builder->get('fixed_price_tax_excluded')->addViewTransformer(new SpecificPriceFixedPriceTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Impact on price', 'Admin.Catalog.Feature'),
            'label_subtitle' => $this->trans('At least one of the following must be activated', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h4',
            'error_bubbling' => false,
            'validation_groups' => function (FormInterface $form): array {
                $validationGroups = ['Default'];
                if ($this->isUsingFixedPrice($form->getData())) {
                    $validationGroups[] = self::FIXED_PRICE_GROUP;
                }
                if ($this->isUsingReduction($form->getData())) {
                    $validationGroups[] = self::REDUCTION_GROUP;
                }

                return $validationGroups;
            },
            'constraints' => [
                new Callback([
                    'callback' => function (?array $impactData, ExecutionContextInterface $context) {
                        $this->validatePriceIsDefined($impactData, $context);
                    },
                ]),
            ],
        ]);
    }

    private function validatePriceIsDefined(?array $impactData, ExecutionContextInterface $context): void
    {
        $isUsingFixedPrice = $this->isUsingFixedPrice($impactData);
        $isUsingReduction = $this->isUsingReduction($impactData);
        if (!$isUsingFixedPrice && !$isUsingReduction) {
            $context
                ->buildViolation($this->trans('Apply a discount to the initial price or set a specific price.', 'Admin.Catalog.Feature'))
                ->addViolation()
            ;
        }
    }

    private function shouldFixedPriceBeDisabled(FormInterface $form): bool
    {
        $impactForm = $form->getParent();
        $impactData = $impactForm->getData();

        return !$this->isUsingFixedPrice($impactData);
    }

    /**
     * Check if fixed price is being setup, the fixed price is based on fixed_price_tax_excluded so if it is absent
     * or if its value equals -1 no fixed price is defined.
     *
     * However, the most trustable value is the one from the checkbox, so it is used as priority when present.
     *
     * @param array|null $impactData
     *
     * @return bool
     */
    private function isUsingFixedPrice(?array $impactData): bool
    {
        if (array_key_exists('disabling_switch_fixed_price_tax_excluded', $impactData)) {
            return $impactData['disabling_switch_fixed_price_tax_excluded'] === true;
        }

        // Use array_key_exists because fixed_price_tax_excluded can be present but null, it doesn't mean it is not
        // used it is just empty
        if (!array_key_exists('fixed_price_tax_excluded', $impactData)) {
            return false;
        }

        // Use 0 as fallback in case the value is null, because empty string throws an exception in DecimalNumber used
        // in InitialPrice::isInitialPriceValue
        $fixedPrice = $impactData['fixed_price_tax_excluded'] ?? 0;

        return !InitialPrice::isInitialPriceValue((string) $fixedPrice);
    }

    private function shouldReductionBeDisabled(FormInterface $form): bool
    {
        $impactForm = $form->getParent();
        $impactData = $impactForm->getData();
        $isUsingReduction = $this->isUsingReduction($impactData);

        return !$isUsingReduction;
    }

    /**
     * Check if reduction is being setup, the reduction is based on reduction value so if it is absent
     * or if its value equals 0 no reduction is defined.
     *
     * However, the most trustable value is the one from the checkbox, so it is used as priority when present.
     *
     * @param array|null $impactData
     *
     * @return bool
     */
    private function isUsingReduction(?array $impactData): bool
    {
        if (array_key_exists('disabling_switch_reduction', $impactData)) {
            return $impactData['disabling_switch_reduction'] === true;
        }

        return !empty($impactData['reduction']['value']);
    }
}
