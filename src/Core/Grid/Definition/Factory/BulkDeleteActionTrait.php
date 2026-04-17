<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;

/**
 * Trait to help build the bulk delete action
 */
trait BulkDeleteActionTrait
{
    /**
     * @param string $bulkDeleteRouteName
     * @param array $options
     *
     * @return BulkActionInterface
     */
    protected function buildBulkDeleteAction(string $bulkDeleteRouteName, array $options = [])
    {
        $options = array_merge(
            [
                'submit_route' => $bulkDeleteRouteName,
                'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                'modal_options' => [],
            ],
            $options
        );
        $options['modal_options'] = new ModalOptions(array_merge(
            [
                'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                'confirm_button_class' => 'btn-danger',
            ],
            $options['modal_options']
        ));

        return (new SubmitBulkAction('delete_selection'))
            ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
            ->setOptions($options)
        ;
    }

    /**
     * Shortcut method to translate text.
     *
     * @param string $id
     * @param array $options
     * @param string $domain
     *
     * @return string
     */
    abstract protected function trans($id, array $options, $domain);
}
