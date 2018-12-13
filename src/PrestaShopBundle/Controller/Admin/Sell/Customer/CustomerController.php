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
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerDefaultGroupAccessException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerTransformationException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
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
            'isSingleShopContext' => $this->get('prestashop.adapter.shop.context')->isSingleShopContext(),
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
        if (!$this->get('prestashop.adapter.shop.context')->isSingleShopContext()) {
            return $this->redirectToRoute('admin_customers_index');
        }

        $this->addGroupSelectionToRequest($request);

        $customerForm = $this->get('prestashop.core.form.identifiable_object.builder.customer_form_builder')->getForm();
        $customerForm->handleRequest($request);

        $customerFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.customer_form_handler');

        try {
            $result = $customerFormHandler->handle($customerForm);

            if ($customerId = $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customers_edit', [
                    'customerId' => $customerId,
                ]);
            }
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Sell/Customer/create.html.twig', [
            'customerForm' => $customerForm->createView(),
            'isB2bFeatureActive' => $this->get('prestashop.core.b2b.b2b_feature')->isActive(),
            'minPasswordLength' => Password::MIN_LENGTH,
        ]);
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
        $this->addGroupSelectionToRequest($request);

        try {
            /** @var CustomerInformation $customerInformation */
            $customerInformation = $this->getQueryBus()->handle(new GetCustomerForViewing(new CustomerId((int) $customerId)));

            $customerFormOptions = [
                'is_password_required' => false,
            ];
            $customerForm = $this->get('prestashop.core.form.identifiable_object.builder.customer_form_builder')
                ->getFormFor((int) $customerId, [], $customerFormOptions)
            ;
            $customerForm->handleRequest($request);

            $customerFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.customer_form_handler');
            $result = $customerFormHandler->handleFor((int) $customerId, $customerForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customers_edit', [
                    'customerId' => $result->getIdentifiableObjectId(),
                ]);
            }
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            if ($e instanceof CustomerNotFoundException) {
                return $this->redirectToRoute('admin_customers_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Customer/edit.html.twig', [
            'customerForm' => $customerForm->createView(),
            'customerInformation' => $customerInformation,
            'isB2bFeatureActive' => $this->get('prestashop.core.b2b.b2b_feature')->isActive(),
            'minPasswordLength' => Password::MIN_LENGTH,
        ]);
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
            } catch (CustomerException $e) {
                $this->addFlash(
                    'error',
                    $this->getErrorMessageForException($e, $this->getErrorMessages($e))
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

        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
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
     * @return FormInterface
     */
    private function getRequiredFieldsForm()
    {
        $requiredFields = $this->getQueryBus()->handle(new GetRequiredFieldsForCustomer());

        return $this->createForm(RequiredFieldsType::class, ['required_fields' => $requiredFields]);
    }

    /**
     * If customer form is submitted and groups are not selected
     * we add empty groups to request
     *
     * @param Request $request
     */
    private function addGroupSelectionToRequest(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_POST)) {
            return;
        }

        if (!$request->request->has('customer')
            || isset($request->request->get('customer')['group_ids'])
        ) {
            return;
        }

        $customerData = $request->request->get('customer');
        $customerData['group_ids'] = [];

        $request->request->set('customer', $customerData);
    }

    /**
     * Get errors that can be used to translate exceptions into user friendly messages
     *
     * @param CustomerException $e
     *
     * @return array
     */
    private function getErrorMessages(CustomerException $e)
    {
        return [
            CustomerNotFoundException::class => $this->trans(
                'This customer does not exist.',
                'Admin.Orderscustomers.Notification'
            ),
            DuplicateCustomerEmailException::class => sprintf(
                '%s %s',
                $this->trans('An account already exists for this email address:', 'Admin.Orderscustomers.Notification'),
                $e instanceof DuplicateCustomerEmailException ? $e->getEmail()->getValue() : ''
            ),
            CustomerDefaultGroupAccessException::class => $this->trans(
                'A default customer group must be selected in group box.',
                'Admin.Orderscustomers.Notification'
            ),
            CustomerConstraintException::class => [
                CustomerConstraintException::INVALID_PASSWORD => $this->trans(
                    'Password should be at least %length% characters long.',
                    'Admin.Orderscustomers.Help',
                    ['%length%' => Password::MIN_LENGTH]
                ),
                CustomerConstraintException::INVALID_FIRST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('First name', 'Admin.Global'))]
                ),
                CustomerConstraintException::INVALID_LAST_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Last name', 'Admin.Global'))]
                ),
                CustomerConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Email', 'Admin.Global'))]
                ),
                CustomerConstraintException::INVALID_BIRTHDAY => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Birthday', 'Admin.Orderscustomers.Feature'))]
                ),
                CustomerConstraintException::INVALID_APE_CODE => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('APE', 'Admin.Orderscustomers.Feature'))]
                ),
            ],
            CustomerTransformationException::class => [
                CustomerTransformationException::CUSTOMER_IS_NOT_GUEST => $this->trans(
                    'This customer already exists as a non-guest.',
                    'Admin.Orderscustomers.Notification'
                ),
                CustomerTransformationException::TRANSFORMATION_FAILED => $this->trans(
                    'An error occurred while updating customer information.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],
        ];
    }
}
