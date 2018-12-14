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

namespace PrestaShopBundle\Controller\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDisableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkEnableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Form\Admin\Sell\Customer\DeleteCustomersType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerController manages "Sell > Customers" page.
 */
class CustomerController extends AbstractAdminController
{
    /**
     * Show customers listing.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to view this."
     * )
     *
     * @param Request $request
     * @param CustomerFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CustomerFilters $filters)
    {
        $customerGridFactory = $this->get('prestashop.core.grid.factory.customer');
        $customerGrid = $customerGridFactory->getGrid($filters);

        $deleteCustomerForm = $this->createForm(DeleteCustomersType::class);

        return $this->render('@PrestaShop/Admin/Sell/Customer/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerGrid' => $this->presentGrid($customerGrid),
            'deleteCustomersForm' => $deleteCustomerForm->createView(),
        ]);
    }

    /**
     * Show customer create form & handle processing of it.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        return $this->redirect(
            $this->getAdminLink($request->attributes->get('_legacy_controller'), [
                'addcustomer' => 1,
            ])
        );
    }

    /**
     * Show customer edit form & handle processing of it.
     *
     * @param int $customerId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($customerId, Request $request)
    {
        return $this->redirect(
            $this->getAdminLink($request->attributes->get('_legacy_controller'), [
                'updatecustomer' => 1,
                'id_customer' => $customerId,
            ])
        );
    }

    /**
     * Show customer details page.
     *
     * @param int $customerId
     * @param Request $request
     *
     * @return Response
     */
    public function viewAction($customerId, Request $request)
    {
        return $this->redirect(
            $this->getAdminLink($request->attributes->get('_legacy_controller'), [
                'viewcustomer' => 1,
                'id_customer' => $customerId,
            ])
        );
    }

    /**
     * Toggle customer status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $customerId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($customerId)
    {
        try {
            $customerId = new CustomerId($customerId);
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->getQueryBus()->handle(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand($customerId);
            $editCustomerCommand->setEnabled(!$editableCustomer->isEnabled());

            $this->getCommandBus()->handle($editCustomerCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->handleCustomerException($e));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Toggle customer newsletter subscription status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $customerId
     *
     * @return RedirectResponse
     */
    public function toggleNewsletterSubscriptionAction($customerId)
    {
        try {
            $customerId = new CustomerId($customerId);
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->getQueryBus()->handle(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand($customerId);
            $editCustomerCommand->setNewsletterSubscribed(!$editableCustomer->isNewsletterSubscribed());

            $this->getCommandBus()->handle($editCustomerCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CustomerException $e) {
            $this->handleCustomerException($e);
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Toggle customer partner offer subscription status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $customerId
     *
     * @return RedirectResponse
     */
    public function togglePartnerOfferSubscriptionAction($customerId)
    {
        try {
            $customerId = new CustomerId($customerId);
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->getQueryBus()->handle(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand($customerId);
            $editCustomerCommand->setPartnerOfferSubscribed(!$editableCustomer->isPartnerOfferSubscribed());

            $this->getCommandBus()->handle($editCustomerCommand);

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->handleCustomerException($e));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Delete customers in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $form = $this->createForm(DeleteCustomersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            try {
                $command = new BulkDeleteCustomerCommand(
                    $data['customers_to_delete'],
                    new CustomerDeleteMethod($data['delete_method'])
                );

                $this->getCommandBus()->handle($command);

                $this->addFlash(
                    'success',
                    $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
                );
            } catch (CustomerException $e) {
                $this->addFlash('error', $this->handleCustomerException($e));
            }
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Delete customer.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $form = $this->createForm(DeleteCustomersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $customerId = (int) reset($data['customers_to_delete']);

            try {
                $command = new DeleteCustomerCommand(
                    new CustomerId($customerId),
                    new CustomerDeleteMethod($data['delete_method'])
                );

                $this->getCommandBus()->handle($command);

                $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
            } catch (CustomerException $e) {
                $this->addFlash('error', $this->handleCustomerException($e));
            }
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Enable customers in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function enableBulkAction(Request $request)
    {
        $customerIds = $request->request->get('customer_customers_bulk', []);

        try {
            $command = new BulkEnableCustomerCommand($customerIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->handleCustomerException($e));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Disable customers in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customers_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function disableBulkAction(Request $request)
    {
        try {
            $customerIds = $request->request->get('customer_customers_bulk', []);

            $command = new BulkDisableCustomerCommand($customerIds);

            $this->getCommandBus()->handle($command);

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->handleCustomerException($e));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Export filtered customers
     *
     * @param CustomerFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(CustomerFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.customer');
        $grid = $gridFactory->getGrid($filters);

        $headers = [
            'id_customer' => $this->trans('ID', 'Admin.Global'),
            'social_title' => $this->trans('Social title', 'Admin.Global'),
            'firstname' => $this->trans('First name', 'Admin.Global'),
            'lastname' => $this->trans('Last name', 'Admin.Global'),
            'email' => $this->trans('Email address', 'Admin.Global'),
            'company' => $this->trans('Company', 'Admin.Global'),
            'total_spent' => $this->trans('Sales', 'Admin.Global'),
            'enabled' => $this->trans('Enabled', 'Admin.Global'),
            'newsletter' => $this->trans('Newsletter', 'Admin.Global'),
            'partner_offers' => $this->trans('Partner offers', 'Admin.Orderscustomers.Feature'),
            'registration' => $this->trans('Registration', 'Admin.Orderscustomers.Feature'),
            'connect' => $this->trans('Last visit', 'Admin.Orderscustomers.Feature'),
        ];

        $data = [];

        foreach ($grid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_customer' => $record['id_customer'],
                'social_title' => '--' === $record['social_title'] ? '' : $record['social_title'],
                'firstname' => $record['firstname'],
                'lastname' => $record['firstname'],
                'email' => $record['firstname'],
                'company' => '--' === $record['company'] ? '' : $record['company'],
                'total_spent' => '--' === $record['total_spent'] ? '' : $record['total_spent'],
                'enabled' => $record['active'],
                'newsletter' => $record['newsletter'],
                'partner_offers' => $record['optin'],
                'registration' => $record['date_add'],
                'connect' => '--' === $record['connect'] ? '' : $record['connect'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('customer_' . date('Y-m-d_His') . '.csv')
        ;
    }

    /**
     * @param CustomerException $e
     *
     * @return string
     */
    protected function handleCustomerException(CustomerException $e)
    {
        $type = get_class($e);

        $errorMessages = [
            CustomerNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($errorMessages[$type])) {
            return $errorMessages[$type];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }
}
