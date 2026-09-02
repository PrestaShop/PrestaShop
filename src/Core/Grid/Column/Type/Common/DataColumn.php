<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Column defines most simple column in the grid that renders raw data.
 */
class DataColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'data';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'field',
            ])
            ->setDefaults([
                'clickable' => true,
                'max_displayed_characters' => 0,
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedTypes('max_displayed_characters', 'int')
        ;
    }
}
