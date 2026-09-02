<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Allows adding Preview functionality to grid rows
 */
class PreviewColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'preview';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'icon_expand',
                'icon_collapse',
                'preview_data_route',
            ])
            ->setDefined([
                'preview_route_params',
            ])
            ->setAllowedTypes('preview_data_route', 'string')
            ->setAllowedTypes('preview_route_params', 'array')
            ->setAllowedTypes('icon_expand', 'string')
            ->setAllowedTypes('icon_collapse', 'string')
        ;
    }
}
