<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductPriceType extends AbstractType
{
    /**
     * @var array
     */
    private $taxRuleGroupChoices;

    /**
     * @var array
     */
    private $taxRuleGroupChoicesAttributes;

    /**
     * @param array $taxRuleGroupChoices
     * @param array $taxRuleGroupChoicesAttributes
     */
    public function __construct(
        array $taxRuleGroupChoices,
        array $taxRuleGroupChoicesAttributes
    ) {
        $this->taxRuleGroupChoices = $taxRuleGroupChoices;
        $this->taxRuleGroupChoicesAttributes = $taxRuleGroupChoicesAttributes;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price_tax_excluded', NumberType::class, [
                'attr' => [
                    'class' => 'product-price-tax-excl',
                ],
            ])
            ->add('price_tax_included', NumberType::class, [
                'attr' => [
                    'class' => 'product-price-tax-incl',
                ],
            ])
            ->add('tax_rule_group', ChoiceType::class, [
                'attr' => [
                    'class' => 'product-tax-rule-group-selection',
                ],
                'choices' => $this->taxRuleGroupChoices,
                'choice_attr' => $this->taxRuleGroupChoicesAttributes,
            ])
        ;
    }
}
