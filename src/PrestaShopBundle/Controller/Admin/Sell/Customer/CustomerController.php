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

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SavePrivateNoteForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\TransformGuestToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerTransformationException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Customer\RequiredFieldsType;
use PrestaShopBundle\Form\Admin\Sell\Customer\TransferGuestAccountType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request $request
     * @param CustomerFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CustomerFilters $filters)
    {
        $customersKpiFactory = $this->get('prestashop.core.kpi_row.factory.customers');

        $customerGridFactory = $this->get('prestashop.core.grid.factory.customer');
        $customerGrid = $customerGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Customer/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerGrid' => $this->presentGrid($customerGrid),
            'customersKpi' => $customersKpiFactory->build(),
            'customerRequiredFieldsForm' => $this->getRequiredFieldsForm()->createView(),
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
     * View customer information.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_customers_index")
     * @DemoRestricted(redirectRoute="admin_customers_index")
     *
     * @param int $customerId
     * @param Request $request
     *
     * @return Response
     */
    public function viewAction($customerId, Request $request)
    {
        try {
            /** @var CustomerInformation $customerInformation */
            $customerInformation = $this->getQueryBus()->handle(new GetCustomerForViewing(new CustomerId((int) $customerId)));
        } catch (CustomerNotFoundException $e) {
            $this->addFlash(
                'error',
                $this->trans('This customer does not exist.', 'Admin.Orderscustomers.Notification')
            );

            return $this->redirectToRoute('admin_customers_index');
        }

        $transferGuestAccountForm = null;
        if ($customerInformation->getPersonalInformation()->isGuest()) {
            $transferGuestAccountForm = $this->createForm(TransferGuestAccountType::class, [
                'id_customer' => $customerId,
            ])->createView();
        }

        $privateNoteForm = $this->createForm(PrivateNoteType::class, [
            'note' => $customerInformation->getGeneralInformation()->getPrivateNote(),
        ]);

        return $this->render('@PrestaShop/Admin/Sell/Customer/view.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerInformation' => $customerInformation,
            'isMultistoreEnabled' => $this->get('prestashop.adapter.feature.multistore')->isActive(),
            'transferGuestAccountForm' => $transferGuestAccountForm,
            'privateNoteForm' => $privateNoteForm->createView(),
        ]);
    }

    /**
     * Save private note for customer.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create'], request.get('_legacy_controller'))",
     *      redirectRoute="admin_customers_index"
     * )
     *
     * @param int $customerId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function savePrivateNoteAction($customerId, Request $request)
    {
        $privateNoteForm = $this->createForm(PrivateNoteType::class);
        $privateNoteForm->handleRequest($request);

        if ($privateNoteForm->isSubmitted()) {
            $data = $privateNoteForm->getData();

            try {
                $this->getCommandBus()->handle(new SavePrivateNoteForCustomerCommand(
                    new CustomerId((int) $customerId),
                    $data['note']
                ));

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            } catch (CustomerNotFoundException $e) {
                $this->addFlash(
                    'error',
                    $this->trans('This customer does not exist.', 'Admin.Orderscustomers.Notification')
                );

                return $this->redirectToRoute('admin_customers_index');
            } catch (CustomerException $e) {
                $this->addFlash(
                    'error',
                    $this->getFallbackErrorMessage(get_class($e), $e->getCode())
                );
            }
        }

        return $this->redirectToRoute('admin_customers_view', [
            'customerId' => $customerId,
        ]);
    }

    /**
     * Transforms guest to customer
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create'], request.get('_legacy_controller'))",
     *      redirectRoute="admin_customers_index"
     * )
     *
     * @param int $customerId
     *
     * @return RedirectResponse
     */
    public function transformGuestToCustomerAction($customerId)
    {
        try {
            $this->getCommandBus()->handle(new TransformGuestToCustomerCommand(new CustomerId((int) $customerId)));

            $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));
        } catch (CustomerNotFoundException $e) {
            $this->addFlash(
                'error',
                $this->trans('This customer does not exist.', 'Admin.Orderscustomers.Notification')
            );

            return $this->redirectToRoute('admin_customers_index');
        } catch (CustomerTransformationException $e) {
            $errors = [
                CustomerTransformationException::CUSTOMER_IS_NOT_GUEST => $this->trans('This customer already exists as a non-guest.', 'Admin.Orderscustomers.Notification'),
                CustomerTransformationException::TRANSFORMATION_FAILED => $this->trans('An error occurred while updating customer information.', 'Admin.Orderscustomers.Notification'),
            ];

            $error = isset($errors[$e->getCode()]) ?
                $errors[$e->getCode()] :
                $this->getFallbackErrorMessage(get_class($e), $e->getCode());

            $this->addFlash('error', $error);
        } catch (CustomerException $e) {
            $this->addFlash(
                'error',
                $this->getFallbackErrorMessage(get_class($e), $e->getCode())
            );
        }

        return $this->redirectToRoute('admin_customers_view', [
            'customerId' => $customerId,
        ]);
    }

    /**
     * Sets required fields for customer
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create'], request.get('_legacy_controller'))",
     *      redirectRoute="admin_customers_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function setRequiredFieldsAction(Request $request)
    {
        $requiredFieldsForm = $this->getRequiredFieldsForm();
        $requiredFieldsForm->handleRequest($request);

        if ($requiredFieldsForm->isSubmitted()) {
            $data = $requiredFieldsForm->getData();

            $this->getCommandBus()->handle(new SetRequiredFieldsForCustomerCommand($data['required_fields']));

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Search for customers by query.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get('customer_search');
        $phrases = explode(' ', $query);
        $isRequestFromLegacyPage = !$request->query->has('sf2');

        $customers = $this->getQueryBus()->handle(new SearchCustomers($phrases));

        // if call is made from legacy page
        // it will return response so legacy can understand it
        if ($isRequestFromLegacyPage) {
            return $this->json([
                'found' => !empty($customers),
                'customers' => $customers,
            ]);
        }

        return $this->json($customers);
    }

    /**
     * @return FormInterface
     */
    private function getRequiredFieldsForm()
    {
        $requiredFields = $this->getQueryBus()->handle(new GetRequiredFieldsForCustomer());

        return $this->createForm(RequiredFieldsType::class, ['required_fields' => $requiredFields]);
    }
}
