<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerAddressGridDefinitionFactory defines customer's addresses grid structure.
 */
final class CustomerAddressGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'customer_address';

    /**
     * @var string
     */
    private $backUrl;

    public function __construct(HookDispatcherInterface $hookDispatcher, ?Request $currentRequest)
    {
        parent::__construct($hookDispatcher);
        $this->backUrl = $currentRequest ? $currentRequest->getUri() : '';
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Addresses', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_address'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_address',
                    ])
            )
            ->add(
                (new DataColumn('company'))
                    ->setName($this->trans('Company', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'company',
                    ])
            )
            ->add(
                (new DataColumn('full_name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'full_name',
                    ])
            )
            ->add(
                (new DataColumn('full_address'))
                    ->setName($this->trans('Address', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'full_address',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('country_name'))
                    ->setName($this->trans('Country', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'country_name',
                    ])
            )
            ->add(
                (new DataColumn('phone_number'))
                    ->setName($this->trans('Phone number(s)', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'phone_number',
                        'sortable' => false,
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => 'admin_addresses_edit',
                                    'route_param_name' => 'addressId',
                                    'route_param_field' => 'id_address',
                                    'extra_route_params' => [
                                        'back' => $this->backUrl,
                                    ],
                                    'clickable_row' => true,
                                ])
                        )
                        ->add(
                            $this->buildDeleteAction(
                                'admin_addresses_delete',
                                'addressId',
                                'id_address',
                                'POST',
                                ['back' => $this->backUrl]
                            )
                        ),
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewOptions()
    {
        return (new ViewOptionsCollection())
            ->add('display_name', false);
    }
}
