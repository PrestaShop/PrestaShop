<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zone;

/**
 * This class will provide data from DB / ORM about Zone
 */
class ZoneByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);

        return FormChoiceFormatter::formatFormChoices(
            Zone::getZones($options['active'], $options['active_first']),
            'id_zone',
            'name'
        );
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => false,
            'active_first' => false,
        ]);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('active_first', 'bool');

        return $resolver->resolve($options);
    }
}
