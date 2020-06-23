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

use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnProductException;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\MerchandiseReturnCustomizationColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MerchandiseReturnGridDefinitionFactory builds grid definition for merchandise returns grid.
 */
final class MerchandiseReturnProductsGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    const GRID_ID = 'merchandise_return_products';

    /**
     * @var int
     */
    private $merchandiseReturnId;

    /**
     * MerchandiseReturnProductsGridDefinitionFactory constructor.
     *
     * @param HookDispatcherInterface $hookDispatcher
     * @param RequestStack $requestStack
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        RequestStack $requestStack
    ) {
        parent::__construct($hookDispatcher);
        try {
            $this->setMerchandiseReturnId($requestStack);
        } catch (MerchandiseReturnProductException $e) {
        }
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
        return $this->trans('Merchandise return products', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('merchandise_return_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_order_detail',
                        'extra_fields' => [
                            'id_customization',
                        ],
                    ])
            )
            ->add(
                (new DataColumn('product_reference'))
                    ->setName($this->trans('Reference', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'product_reference',
                    ])
            )
            ->add(
                (new DataColumn('product_name'))
                    ->setName($this->trans('Product name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'product_name',
                    ])
            )
            ->add(
                (new DataColumn('quantity'))
                    ->setName($this->trans('Quantity', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'product_quantity',
                    ])
            )
            ->add(
                (new MerchandiseReturnCustomizationColumn('customizations'))
                    ->setName($this->trans('Customizations', [], 'Admin.Global'))
                    ->setOptions([
                        'customizations' => 'customizations',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_merchandise_returns_delete_product',
                                'route_param_name' => 'merchandiseReturnId',
                                'route_param_field' => 'id_order_return',
                                'extra_route_params' => [
                                    'merchandiseReturnDetailId' => 'id_order_detail',
                                    'customizationId' => 'id_customization',
                                ],
                                'confirm_message' => $this->trans('Are you sure you want to delete the selected item?', [], 'Admin.Notifications.Warning'),
                            ])
                            ),
                    ])
            )
        ;

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('reference', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search Reference', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('product_reference')
            )
            ->add((new Filter('product_name', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search Name', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('product_name')
            )
            ->add((new Filter('quantity', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search quantity', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('quantity')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                    'merchandiseReturnId' => $this->merchandiseReturnId,
                ],
                'redirect_route' => 'admin_merchandise_returns_edit',
                'redirect_route_params' => [
                    'merchandiseReturnId' => $this->merchandiseReturnId,
                ],
            ])
            ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('delete_selection'))
                    ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_merchandise_returns_delete_product_bulk',
                        'route_params' => [
                            'merchandiseReturnId' => $this->merchandiseReturnId,
                        ],
                        'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                        'modal_options' => new ModalOptions([
                            'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                            'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                            'confirm_button_class' => 'btn-danger',
                        ]),
                    ])
            )
            ;
    }

    /**
     * Sets cms page category parent id directly from request attribute. On not found case, it assigns the default one.
     *
     * @param RequestStack $requestStack
     *
     * @throws MerchandiseReturnProductException
     */
    private function setMerchandiseReturnId(RequestStack $requestStack): void
    {
        $request = $requestStack->getCurrentRequest();

        if (null !== $request && $request->attributes->has('merchandiseReturnId')) {
            $this->merchandiseReturnId = $request->attributes->get('merchandiseReturnId');
        } else {
            throw new MerchandiseReturnProductException('merchandiseReturnId attribute does not exist');
        }
    }
}
