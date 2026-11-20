<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;

/**
 * Defines the grid listing the products a file is attached to
 */
final class AttachmentProductGridDefinitionFactory extends AbstractAssociatedProductGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'attachment_product';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getIndexRoute(): string
    {
        return 'admin_attachments_edit';
    }

    protected function getEntityIdRouteParam(): string
    {
        return 'attachmentId';
    }

    protected function getColumns(): ColumnCollectionInterface
    {
        return parent::getColumns()
            ->addBefore(
                'id_product',
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_product',
                    ])
            )
        ;
    }

    protected function getRowActions(): RowActionCollectionInterface
    {
        return parent::getRowActions()
            ->add(
                $this->buildDeleteAction(
                    'admin_attachments_remove_product',
                    'productId',
                    'id_product',
                    'POST',
                    ['attachmentId' => $this->associatedEntityId],
                    $this->getRemoveOptions(),
                    $this->trans('Remove from file', [], 'Admin.Catalog.Feature'),
                    'remove',
                    'link_off'
                )
            )
        ;
    }

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction(
                    'admin_attachments_remove_products_bulk',
                    array_merge($this->getRemoveOptions(), ['route_params' => ['attachmentId' => $this->associatedEntityId]]),
                    'remove_selection',
                    $this->trans('Remove selected from file', [], 'Admin.Catalog.Feature')
                )
            )
        ;
    }

    /**
     * @return array<string, mixed>
     */
    private function getRemoveOptions(): array
    {
        return [
            'confirm_message' => $this->trans('Remove the selected product(s) from this file? The products and the file are kept.', [], 'Admin.Catalog.Notification'),
            'modal_options' => [
                'title' => $this->trans('Remove from file', [], 'Admin.Catalog.Feature'),
                'confirm_button_label' => $this->trans('Remove', [], 'Admin.Actions'),
            ],
        ];
    }
}
