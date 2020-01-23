<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     *
     * @return BulkActionInterface
     */
    protected function buildBulkDeleteAction(string $bulkDeleteRouteName)
    {
        return (new SubmitBulkAction('delete_selection'))
            ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => $bulkDeleteRouteName,
                'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                'modal_options' => new ModalOptions([
                    'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                    'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                    'confirm_button_class' => 'btn-danger',
                ]),
            ])
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
