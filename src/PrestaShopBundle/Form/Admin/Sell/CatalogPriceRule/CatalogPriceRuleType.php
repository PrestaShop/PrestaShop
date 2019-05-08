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
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CatalogPriceRuleType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

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
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @var array
     */
    private $contextShopIds;//@todo: shopByIdChoiceProvider

    /**
     * @param TranslatorInterface $translator
     * @param array $currencyByIdChoices
     * @param array $countryByIdChoices
     * @param array $groupByIdChoices
     * @param $isSingleShopContext
     * @param array $contextShopIds
     */
    public function __construct(
        TranslatorInterface $translator,
        array $currencyByIdChoices,
        array $countryByIdChoices,
        array $groupByIdChoices,
        $isSingleShopContext,
        array $contextShopIds
    ) {
        $this->translator = $translator;
        $this->currencyByIdChoices = $currencyByIdChoices;
        $this->countryByIdChoices = $countryByIdChoices;
        $this->groupByIdChoices = $groupByIdChoices;
        $this->isSingleShopContext = $isSingleShopContext;
        $this->contextShopIds = $contextShopIds;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new CleanHtml([
                        'message' => $this->translator->trans(
                            '%s is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_currency', ChoiceType::class, [
                'required' => false,
                'choices' => $this->currencyByIdChoices,
            ])
            ->add('id_country', ChoiceType::class, [
                'required' => false,
                'choices' => $this->countryByIdChoices,
            ])
            ->add('id_group', ChoiceType::class, [
                'required' => false,
                'choices' => $this->groupByIdChoices,
            ])
            ->add('from_quantity', NumberType::class)
            ->add('price', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'negative_price',
                    ]),
                ],
            ])
            ->add('leave_initial_price', CheckboxType::class, [
                'required' => false,
            ])
            ->add('from', DatePickerType::class, [
                'required' => false,
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
            ])
            ->add('include_tax', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    $this->translator->trans('Tax included', [], 'Admin.Global') => 1,
                    $this->translator->trans('Tax excluded', [], 'Admin.Global') => 0,
                ]
            ])
            ->add('reduction_type', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    $this->translator->trans('Percentage', [], 'Admin.Global') => 'percentage',
                    $this->translator->trans('Amount', [], 'Admin.Global') => 'amount',
                ],
            ])
            ->add('reduction', NumberType::class, [
                'constraints' => [
                    new TypedRegex([
                        'type' => 'price',
                    ]),
                ],
            ])
        ;

        if (!$this->isSingleShopContext) {
            $builder->add('id_shop', ChoiceType::class, [
                'choices' => $this->contextShopIds //@todo
            ]);
        }
    }
}
