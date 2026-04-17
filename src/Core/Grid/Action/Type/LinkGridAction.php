<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\AbstractGridAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LinkGridAction defines grid action which is link.
 */
final class LinkGridAction extends AbstractGridAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'route',
            ])
            ->setDefaults([
                'route_params' => [],
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_params', 'array');
    }
}
