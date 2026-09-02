<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Boolean column allows adding boolean columns (Yes/No, On/Off and etc) to grid
 */
class BooleanColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'boolean';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'field',
                'true_name',
                'false_name',
            ])
            ->setDefaults([
                'clickable' => false,
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('true_name', 'string')
            ->setAllowedTypes('false_name', 'string')
            ->setAllowedTypes('clickable', 'bool')
        ;
    }
}
