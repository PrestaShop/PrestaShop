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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Country;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopException;
use State;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides choices of country states with state name as key and id as value
 */
final class CountryStateByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);
        $choices = [];

        try {
            $countryId = $resolvedOptions['id_country'];
            $countryHasStates = (new Country($countryId))->contains_states;

            if (!$countryHasStates) {
                return [];
            }

            $states = State::getStatesByIdCountry($countryId, $resolvedOptions['only_active']);

            foreach ($states as $state) {
                $choices[$state['name']] = $state['id_state'];
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf('An error occurred when getting states for country id "%s"', $countryId));
        }

        return $choices;
    }

    /**
     * Configures array parameters and default values
     *
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['only_active' => false]);
        $resolver->setRequired('id_country');
        $resolver->setAllowedTypes('id_country', 'int');
        $resolver->setAllowedTypes('only_active', 'bool');
        $this->allowIdCountryGreaterThanZero($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function allowIdCountryGreaterThanZero(OptionsResolver $resolver)
    {
        $resolver->setAllowedValues('id_country', function ($value) {
            return 0 < $value;
        });
    }
}
