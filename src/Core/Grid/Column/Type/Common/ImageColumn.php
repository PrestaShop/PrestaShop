<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageColumn renders column as image.
 */
final class ImageColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'src_field',
            ])
            ->setDefaults([
                'clickable' => true,
                'alt_field' => '',
            ])
            ->setAllowedTypes('src_field', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedTypes('alt_field', 'string')
        ;
    }
}
