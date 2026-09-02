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

            $choices = FormChoiceFormatter::formatFormChoices(
                State::getStatesByIdCountry($countryId, $resolvedOptions['only_active'], 'name', 'asc'),
                'id_state',
                'name'
            );
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
