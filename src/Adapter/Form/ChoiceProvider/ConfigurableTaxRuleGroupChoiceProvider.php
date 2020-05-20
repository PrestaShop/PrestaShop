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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TaxRulesGroup;

class ConfigurableTaxRuleGroupChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getChoices(array $options)
    {
        $options = $this->resolveOptions($options);
        $rules = TaxRulesGroup::getTaxRulesGroupsForOptions($options['with_rates']);

        $choices = [];
        foreach ($rules as $rule) {
            if (!$options['with_rates']) {
                $choices[$rule['name']] = $rule['id_tax_rules_group'];

                continue;
            }

            $choices[$rule['name']] = [
                'id' => $rule['id_tax_rules_group'],
                'rate' => !empty($rule['rate']) ? $rule['rate'] : null,
            ];
        }

        return $choices;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'with_rates' => false,
            ])
            ->setAllowedTypes('with_rates', 'bool');

        return $resolver->resolve($options);
    }
}
