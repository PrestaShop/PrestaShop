<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class provides configurable country choices with ID values.
 */
final class CountryByIdConfigurableChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param int $langId
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        int $langId,
        CountryDataProvider $countryDataProvider
    ) {
        $this->langId = $langId;
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * Get country choices.
     *
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);

        return FormChoiceFormatter::formatFormChoices(
            $this->countryDataProvider->getCountries(
                $this->langId,
                $options['active'],
                $options['contains_states'],
                $options['list_states']
            ),
            'id_country',
            'name'
        );
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => false,
            'contains_states' => false,
            'list_states' => false,
        ]);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('contains_states', 'bool');
        $resolver->setAllowedTypes('list_states', 'bool');

        return $resolver->resolve($options);
    }
}
