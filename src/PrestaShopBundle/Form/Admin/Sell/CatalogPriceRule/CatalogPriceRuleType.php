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

namespace PrestaShopBundle\Form\Admin\Sell\CatalogPriceRule;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\PriceReductionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines catalog price rule form for create/edit actions
 */
class CatalogPriceRuleType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $isMultiShopEnabled;

    /**
     * @var array
     */
    private $groupByIdChoices;

    /**
     * @var array
     */
    private $shopByIdChoices;

    /**
     * @param TranslatorInterface $translator
     * @param bool $isMultiShopEnabled
     * @param array $groupByIdChoices
     * @param array $shopByIdChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        bool $isMultiShopEnabled,
        array $groupByIdChoices,
        array $shopByIdChoices
    ) {
        $this->translator = $translator;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
        $this->groupByIdChoices = $groupByIdChoices;
        $this->shopByIdChoices = $shopByIdChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new CleanHtml(),
                ],
            ])
            ->add('id_currency', CurrencyChoiceType::class, [
                'add_all_currencies_option' => true,
            ])
            ->add('id_country', CountryChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'add_all_countries_option' => true,
            ])
            ->add('id_group', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedGroupChoices(),
            ])
            ->add('from_quantity', NumberType::class, [
                'scale' => 0,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->translator->trans(
                            '%s is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'scale' => 6,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->translator->trans(
                            '%s is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('leave_initial_price', CheckboxType::class, [
                'required' => false,
            ])
            ->add('date_range', DateRangeType::class, [
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
                'placeholder' => $this->translator->trans('YYYY-MM-DD HH:mm:ss', [], 'Admin.Global'),
                'constraints' => [
                    new DateRange([
                        'message' => $this->translator->trans(
                            'The selected date range is not valid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('reduction', PriceReductionType::class, [
                'constraints' => [
                    new Reduction([
                        'invalidPercentageValueMessage' => $this->translator->trans(
                            'Reduction value "%value%" is invalid. Allowed values from 0 to %max%',
                            ['%max%' => ReductionVO::MAX_ALLOWED_PERCENTAGE . '%'],
                            'Admin.Notifications.Error'
                        ),
                        'invalidAmountValueMessage' => $this->translator->trans(
                            'Reduction value "%value%" is invalid. Value cannot be negative',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;

        if ($this->isMultiShopEnabled) {
            $builder->add('id_shop', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->shopByIdChoices,
            ]);
        }
    }

    /**
     * Prepends 'All groups' option with id of 0 to group choices
     *
     * @return array
     */
    private function getModifiedGroupChoices(): array
    {
        return array_merge(
            [$this->translator->trans('All groups', [], 'Admin.Global') => 0],
            $this->groupByIdChoices
        );
    }
}
