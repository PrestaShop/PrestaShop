<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BulkAction holds data about single bulk action available in grid.
 */
final class SubmitBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'submit';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'submit_route',
            ])
            ->setDefaults([
                'confirm_message' => null,
                'modal_options' => null,
                'submit_method' => 'POST',
                'route_params' => [],
            ])
            ->setAllowedTypes('submit_route', 'string')
            ->setAllowedTypes('confirm_message', ['string', 'null'])
            ->setAllowedTypes('modal_options', [ModalOptions::class, 'null'])
            ->setAllowedValues('submit_method', ['POST', 'GET'])
            ->setAllowedTypes('route_params', 'array');
    }
}
