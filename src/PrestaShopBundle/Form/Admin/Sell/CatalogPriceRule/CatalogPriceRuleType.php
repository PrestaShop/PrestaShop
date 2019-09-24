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

namespace PrestaShopBundle\Form\Admin\Sell\CatalogPriceRule;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\ReductionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction as ReductionVO;

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
    private $isMultishopEnabled;

    /**
     * @var array
     */
    private $currencyByIdChoices;

    /**
     * @var array
     */
    private $countryByIdChoices;

    /**
     * @var array
     */
    private $groupByIdChoices;

    /**
     * @var array
     */
    private $shopByIdChoices;

    /**
     * @var array
     */
    private $taxInclusionChoices;

    /**
     * @param TranslatorInterface $translator
     * @param bool $isMultishopEnabled
     * @param array $currencyByIdChoices
     * @param array $countryByIdChoices
     * @param array $groupByIdChoices
     * @param array $shopByIdChoices
     * @param array $taxInclusionChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        bool $isMultishopEnabled,
        array $currencyByIdChoices,
        array $countryByIdChoices,
        array $groupByIdChoices,
        array $shopByIdChoices,
        array $taxInclusionChoices
    ) {
        $this->translator = $translator;
        $this->isMultishopEnabled = $isMultishopEnabled;
        $this->currencyByIdChoices = $currencyByIdChoices;
        $this->countryByIdChoices = $countryByIdChoices;
        $this->groupByIdChoices = $groupByIdChoices;
        $this->shopByIdChoices = $shopByIdChoices;
        $this->taxInclusionChoices = $taxInclusionChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateTimeFormat = 'YYYY-MM-DD HH:mm:ss';

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new CleanHtml(),
                ],
            ])
            ->add('id_currency', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedCurrencyChoices(),
            ])
            ->add('id_country', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedCountryChoices(),
            ])
            ->add('id_group', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->getModifiedGroupChoices(),
            ])
            ->add('from_quantity', TextType::class)
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
            ->add('from', DatePickerType::class, [
                'required' => false,
                'date_format' => $dateTimeFormat,
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
                'date_format' => $dateTimeFormat,
            ])
            ->add('include_tax', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $this->taxInclusionChoices,
            ])
            ->add('reduction', ReductionType::class, [
                'constraints' => [
                    new Reduction([
                        'invalidPercentageValueMessage' => $this->translator->trans(
                            'Reduction cannot be higher than %max_reduction%',
                            ['%max_reduction%' => ReductionVO::MAX_ALLOWED_PERCENTAGE . '%'],
                            'Admin.Notifications.Error'
                        ),
                        'invalidAmountValueMessage' => $this->translator->trans(
                            'Reduction cannot be less than %max_reduction%',
                            ['%max_reduction%' => 0],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;

        if ($this->isMultishopEnabled) {
            $builder->add('id_shop', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->shopByIdChoices,
            ]);
        }
    }

    /**
     * Prepends 'All currencies' option with id of 0 to currency choices
     *
     * @return array
     */
    private function getModifiedCurrencyChoices(): array
    {
        return array_merge(
            [$this->translator->trans('All currencies', [], 'Admin.Global') => 0],
            $this->currencyByIdChoices
        );
    }

    /**
     * Prepends 'All countries' option with id of 0 to country choices
     *
     * @return array
     */
    private function getModifiedCountryChoices(): array
    {
        return array_merge(
            [$this->translator->trans('All countries', [], 'Admin.Global') => 0],
            $this->countryByIdChoices
        );
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
