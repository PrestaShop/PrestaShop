<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays choices in the grid.
 */
final class ChoiceColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'choice';
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(
                [
                    'choice_provider',
                    'field',
                    'route',
                ]
            )
            ->setDefaults([
                'color_field' => '',
                'record_route_params' => [],
            ])
            ->setAllowedTypes('choice_provider', ConfigurableFormChoiceProviderInterface::class)
            ->setAllowedTypes('field', ['string', 'int', 'bool'])
            ->setAllowedTypes('color_field', 'string')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('record_route_params', 'array')
        ;
    }
}
