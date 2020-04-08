<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;

/**
 * Trait to help build the grid single line delete action
 */
trait DeleteActionTrait
{
    protected function buildDeleteAction(
        string $deleteRouteName,
        string $deleteRouteParamName,
        string $deleteRouteParamField,
        string $method = 'POST',
        array $extraRouteParams = [],
        array $translations = []
    ): RowActionInterface {
        return (new SubmitRowAction('delete'))
            ->setName($this->trans('Delete', [], 'Admin.Actions'))
            ->setIcon('delete')
            ->setOptions([
                'route' => $deleteRouteName,
                'route_param_name' => $deleteRouteParamName,
                'route_param_field' => $deleteRouteParamField,
                'extra_route_params' => $extraRouteParams,
                'confirm_message' => array_key_exists('confirm_message', $translations) ?
                    $translations['confirm_message'] :
                    $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                'method' => $method,
                'modal_options' => new ModalOptions([
                    'title' => array_key_exists('modal_options.title', $translations) ?
                        $translations['modal_options.title'] :
                        $this->trans('Delete selection', [], 'Admin.Actions'),
                    'confirm_button_label' => array_key_exists('modal_options.confirm_button_label', $translations) ?
                        $translations['modal_options.confirm_button_label'] :
                        $this->trans('Delete', [], 'Admin.Actions'),
                    'close_button_label' => array_key_exists('modal_options.close_button_label', $translations) ?
                        $translations['modal_options.close_button_label'] :
                        $this->trans('Cancel', [], 'Admin.Actions'),
                    'confirm_button_class' => 'btn-danger',
                ]),
            ])
        ;
    }

    /**
     * Shortcut method to translate text.
     */
    abstract protected function trans($id, array $options, $domain);
}
