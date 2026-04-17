<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Columns is used as identifier in grid (e.g. Product ID, Category ID & etc)
 */
final class IdentifierColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'identifier';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'identifier_field',
            ])
            ->setDefaults([
                'sortable' => true,
                'with_bulk_field' => false,
                'bulk_field' => null,
                'preview' => null,
                'clickable' => true,
            ])
            ->setAllowedTypes('identifier_field', 'string')
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('with_bulk_field', 'bool')
            ->setAllowedTypes('bulk_field', ['string', 'null'])
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedValues('preview', function ($previewColumn) {
                return $previewColumn instanceof PreviewColumn || $previewColumn === null;
            })
        ;
    }
}
