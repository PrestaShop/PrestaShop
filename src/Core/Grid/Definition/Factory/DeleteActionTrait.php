<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        array $options = [],
        ?string $actionLabel = null
    ): RowActionInterface {
        $options = array_merge(
            [
                'route' => $deleteRouteName,
                'route_param_name' => $deleteRouteParamName,
                'route_param_field' => $deleteRouteParamField,
                'extra_route_params' => $extraRouteParams,
                'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                'method' => $method,
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

        return (new SubmitRowAction('delete'))
            ->setName($actionLabel ?? $this->trans('Delete', [], 'Admin.Actions'))
            ->setIcon('delete')
            ->setOptions($options)
        ;
    }

    /**
     * Shortcut method to translate text.
     */
    abstract protected function trans($id, array $options, $domain);
}
