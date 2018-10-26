<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotToggleSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Search\Filters\SupplierFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SupplierController is responsible for "Sell > Catalog > Brands & Suppliers > Suppliers" page.
 */
class SupplierController extends FrameworkBundleAdminController
{
    /**
     * Show suppliers listing.
     *
     * @Template("@PrestaShop/Admin/Sell/Catalog/Suppliers/index.html.twig")
     *
     * @param SupplierFilters $filters
     *
     * @return array
     */
    public function indexAction(SupplierFilters $filters)
    {
        $supplierGridFactory = $this->get('prestashop.core.grid.factory.supplier');

        $supplierGrid = $supplierGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'supplierGrid' => $gridPresenter->present($supplierGrid),
        ];
    }

    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.supplier');
        $supplierDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($supplierDefinition);

        $searchParametersForm->handleRequest($request);
        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_suppliers_index', ['filters' => $filters]);
    }

    public function createAction()
    {
        $legacyLink = $this->getAdminLink('AdminSuppliers', [
            'addsupplier' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    public function deleteAction()
    {
        //todo: implement
    }

    public function editAction($supplierId)
    {
        $legacyLink = $this->getAdminLink('AdminSuppliers', [
            'id_supplier' => $supplierId,
            'updatesupplier' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    public function toggleStatusAction($supplierId)
    {
        try {
            $supplierId = new SupplierId($supplierId);
            $this->getCommandBus()->handle(new ToggleSupplierStatusCommand($supplierId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (SupplierException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_suppliers_index');
    }

    public function viewAction()
    {
        //todo: implement
    }

    /**
     * Gets error by exception type.
     *
     * @param SupplierException $exception
     *
     * @return string
     */
    private function handleException(SupplierException $exception)
    {
        $exceptionTypeDictionary = [
            SupplierNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotToggleSupplierStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
        ];

        $exceptionType = get_class($exception);
        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
    }
}
