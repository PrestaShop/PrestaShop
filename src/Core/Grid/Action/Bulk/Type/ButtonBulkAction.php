<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ButtonBulkAction holds data to display a simple button, it allows you to
 * set a (required) class and additional (optional) attributes that can then be used
 * in javascript.
 */
final class ButtonBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'button';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'class',
            ])
            ->setDefaults([
                'attributes' => [],
            ])
            ->setAllowedTypes('class', 'string')
            ->setAllowedTypes('attributes', 'array');
    }
}
