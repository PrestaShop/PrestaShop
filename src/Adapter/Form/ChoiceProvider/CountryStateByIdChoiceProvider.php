<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Country;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShopException;
use State;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Validate;

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

        $countryId = $resolvedOptions['id_country'];
        try {
            $countryHasStates = (new Country($countryId))->contains_states;

            if (!$countryHasStates) {
                return [];
            }

            $states = State::getStatesByIdCountry($countryId, $resolvedOptions['only_active'], 'name', 'asc');

            /*
             * A record already pointing at a state that has since been disabled must keep it in the list.
             * Without it the select renders with nothing chosen, and saving the form for an unrelated
             * reason moves the record to whichever state the browser submits instead.
             */
            $keptStateId = $resolvedOptions['kept_state_id'];
            if ($keptStateId > 0 && !in_array($keptStateId, array_column($states, 'id_state'))) {
                $keptState = new State($keptStateId);
                if (Validate::isLoadedObject($keptState) && (int) $keptState->id_country === $countryId) {
                    $states[] = ['id_state' => $keptState->id, 'name' => $keptState->name];
                }
            }

            $choices = FormChoiceFormatter::formatFormChoices($states, 'id_state', 'name');
        } catch (PrestaShopException) {
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
        // Disabled states are hidden by default: every consumer of this provider is a form asking which
        // state a record belongs to, and the front office has always filtered them out
        // (CustomerAddressFormatter). Pass only_active => false to get the raw list back.
        $resolver->setDefaults(['only_active' => true, 'kept_state_id' => 0]);
        $resolver->setRequired('id_country');
        $resolver->setAllowedTypes('id_country', 'int');
        $resolver->setAllowedTypes('only_active', 'bool');
        $resolver->setAllowedTypes('kept_state_id', 'int');
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
