<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Customer;

use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\B2b\B2bFeature;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDisableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkEnableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetPrivateNoteAboutCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\TransformGuestToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerByEmailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerDefaultGroupAccessException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerTransformationException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\MissingCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerCarts;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForAddressCreation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerOrders;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressCreationCustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerAddressFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerBoughtProductFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerCartFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerDiscountFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerOrderFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerViewedProductFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Sell\Customer\DeleteCustomersType;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Customer\RequiredFieldsType;
use PrestaShopBundle\Form\Admin\Sell\Customer\TransferGuestAccountType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerController manages "Sell > Customers" page.
 */
class CustomerController extends PrestaShopAdminController
{
    /**
     * Show customers listing.
     *
     * @param Request $request
     * @param CustomerFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to view this.')]
    public function indexAction(
        Request $request,
        CustomerFilters $filters,
        #[Autowire(service: 'prestashop.core.kpi_row.factory.customers')]
        KpiRowFactoryInterface $customersKpiFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer')]
        GridFactoryInterface $customerGridFactory,
    ): Response {
        $customerGrid = $customerGridFactory->getGrid($filters);

        $deleteCustomerForm = $this->createForm(DeleteCustomersType::class);

        $showcaseCardIsClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed($this->getEmployeeContext()->getEmployee()->getId(), ShowcaseCard::CUSTOMERS_CARD)
        );

        return $this->render('@PrestaShop/Admin/Sell/Customer/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerGrid' => $this->presentGrid($customerGrid),
            'customersKpi' => $customersKpiFactory->build(),
            'customerRequiredFieldsForm' => $this->getRequiredFieldsForm()->createView(),
            'isSingleShopContext' => $this->getShopContext()->getShopConstraint()->isSingleShopContext(),
            'deleteCustomersForm' => $deleteCustomerForm->createView(),
            'showcaseCardName' => ShowcaseCard::CUSTOMERS_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClosed,
            'layoutHeaderToolbarBtn' => $this->getCustomerIndexToolbarButtons(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Show customer create form & handle processing of it.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.customer_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.customer_form_handler')]
        FormHandlerInterface $formHandler,
        #[Autowire(service: 'prestashop.adapter.group.provider.default_groups_provider')]
        DefaultGroupsProviderInterface $defaultGroupsProvider,
        B2bFeature $b2bFeature,
    ): Response {
        if (!$this->getShopContext()->getShopConstraint()->isSingleShopContext()) {
            return $this->redirectToRoute('admin_customers_index');
        }

        $this->addGroupSelectionToRequest($request);
        $customerForm = $formBuilder->getForm(
            [],
            [
                'show_guest_field' => (bool) $this->getConfiguration()->get('PS_GUEST_CHECKOUT_ENABLED'),
            ]
        );
        $customerForm->handleRequest($request);

        try {
            $result = $formHandler->handle($customerForm);

            if ($customerId = $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                if ($request->query->has('submitFormAjax')) {
                    /** @var ViewableCustomer $customerInformation */
                    $customerInformation = $this->dispatchQuery(new GetCustomerForViewing((int) $customerId));

                    return $this->render('@PrestaShop/Admin/Sell/Customer/modal_create_success.html.twig', [
                        'customerId' => $customerId,
                        'customerEmail' => $customerInformation->getPersonalInformation()->getEmail(),
                    ]);
                }

                return $this->redirectToRoute('admin_customers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        // Get default groups for JS purposes
        $defaultGroups = $defaultGroupsProvider->getGroups();

        return $this->render('@PrestaShop/Admin/Sell/Customer/create.html.twig', [
            'customerForm' => $customerForm->createView(),
            'isB2bFeatureActive' => $b2bFeature->isActive(),
            'minPasswordLength' => Password::MIN_LENGTH,
            'displayInIframe' => $request->query->has('submitFormAjax'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New customer', [], 'Admin.Navigation.Menu'),
            'defaultGroups' => [
                $defaultGroups->getVisitorsGroup()->getId(),
                $defaultGroups->getGuestsGroup()->getId(),
                $defaultGroups->getCustomersGroup()->getId(),
            ],
            'customerGroupId' => $defaultGroups->getCustomersGroup()->getId(),
            'guestGroupId' => $defaultGroups->getGuestsGroup()->getId(),
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
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index')]
    public function editAction(
        int $customerId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.customer_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.customer_form_handler')]
        FormHandlerInterface $formHandler,
        B2bFeature $b2bFeature,
    ): Response {
        $this->addGroupSelectionToRequest($request);
        /** @var EditableCustomer $customerInformation */
        $customerInformation = $this->dispatchQuery(new GetCustomerForEditing($customerId));
        $customerFormOptions = [
            'is_password_required' => false,
            'show_guest_field' => false,
        ];

        try {
            $customerForm = $formBuilder->getFormFor((int) $customerId, [], $customerFormOptions);
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );

            return $this->redirectToRoute('admin_customers_index');
        }

        try {
            $customerForm->handleRequest($request);
            $result = $formHandler->handleFor((int) $customerId, $customerForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            if ($e instanceof CustomerNotFoundException) {
                return $this->redirectToRoute('admin_customers_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Customer/edit.html.twig', [
            'customerForm' => $customerForm->createView(),
            'customerInformation' => $customerInformation,
            'isB2bFeatureActive' => $b2bFeature->isActive(),
            'minPasswordLength' => Password::MIN_LENGTH,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing customer %name%',
                [
                    '%name%' => mb_substr($customerInformation->getFirstName()->getValue(), 0, 1) . '. ' . $customerInformation->getLastName()->getValue(),
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    /**
     * View customer information.
     *
     * @param int $customerId
     * @param Request $request
     * @param CustomerDiscountFilters $customerDiscountFilters
     * @param CustomerAddressFilters $customerAddressFilters
     * @param CustomerCartFilters $customerCartFilters
     * @param CustomerOrderFilters $customerOrderFilters
     * @param CustomerBoughtProductFilters $customerBoughtProductFilters
     * @param CustomerViewedProductFilters $customerViewedProductFilters
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_customers_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index')]
    public function viewAction(
        int $customerId,
        Request $request,
        CustomerDiscountFilters $customerDiscountFilters,
        CustomerAddressFilters $customerAddressFilters,
        CustomerCartFilters $customerCartFilters,
        CustomerOrderFilters $customerOrderFilters,
        CustomerBoughtProductFilters $customerBoughtProductFilters,
        CustomerViewedProductFilters $customerViewedProductFilters,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.discount')]
        GridFactoryInterface $customerDiscountGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.address')]
        GridFactoryInterface $customerAddressGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.order')]
        GridFactoryInterface $customerOrderGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.cart')]
        GridFactoryInterface $customerCartGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.bought_product')]
        GridFactoryInterface $customerBoughtProductGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.customer.viewed_product')]
        GridFactoryInterface $customerViewedProductGridFactory,
    ): Response {
        try {
            /** @var ViewableCustomer $customerInformation */
            $customerInformation = $this->dispatchQuery(new GetCustomerForViewing($customerId));
        } catch (CustomerNotFoundException) {
            $this->addFlash(
                'error',
                $this->trans('This customer does not exist.', [], 'Admin.Orderscustomers.Notification')
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

        // Discount listing
        $customerDiscountFilters->addFilter(['id_customer' => $customerId]);
        $customerDiscountGrid = $customerDiscountGridFactory->getGrid($customerDiscountFilters);

        // Addresses listing
        $customerAddressFilters->addFilter(['id_customer' => $customerId]);
        $customerAddressGrid = $customerAddressGridFactory->getGrid($customerAddressFilters);

        // Order listing
        $customerOrderFilters->addFilter(['id_customer' => $customerId]);
        $customerOrderGrid = $customerOrderGridFactory->getGrid($customerOrderFilters);

        // Cart listing
        $customerCartFilters->addFilter(['id_customer' => $customerId]);
        $customerCartGrid = $customerCartGridFactory->getGrid($customerCartFilters);

        // Bought products listing
        $customerBoughtProductFilters->addFilter(['id_customer' => $customerId]);
        $customerBoughtProductGrid = $customerBoughtProductGridFactory->getGrid($customerBoughtProductFilters);

        // Viewed products listing
        $customerViewedProductFilters->addFilter(['id_customer' => $customerId]);
        $customerViewedProductGrid = $customerViewedProductGridFactory->getGrid($customerViewedProductFilters);

        if ($request->query->has('conf')) {
            $this->manageLegacyFlashes($request->query->get('conf'));
        }

        return $this->render('@PrestaShop/Admin/Sell/Customer/view.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerInformation' => $customerInformation,
            'customerDiscountGrid' => $this->presentGrid($customerDiscountGrid),
            'customerAddressGrid' => $this->presentGrid($customerAddressGrid),
            'customerOrderGrid' => $this->presentGrid($customerOrderGrid),
            'customerCartGrid' => $this->presentGrid($customerCartGrid),
            'customerBoughtProductGrid' => $this->presentGrid($customerBoughtProductGrid),
            'customerViewedProductGrid' => $this->presentGrid($customerViewedProductGrid),
            'isMultistoreEnabled' => $this->getShopContext()->isMultiShopEnabled(),
            'transferGuestAccountForm' => $transferGuestAccountForm,
            'privateNoteForm' => $privateNoteForm->createView(),
            'layoutHeaderToolbarBtn' => $this->getCustomerViewToolbarButtons($customerId),
            'layoutTitle' => $this->trans(
                'Customer %name%',
                [
                    '%name%' => mb_substr($customerInformation->getPersonalInformation()->getFirstName(), 0, 1) . '. ' . $customerInformation->getPersonalInformation()->getLastName(),
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    /**
     * Set private note about customer.
     *
     * @param int $customerId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index')]
    public function setPrivateNoteAction(int $customerId, Request $request): RedirectResponse
    {
        $privateNoteForm = $this->createForm(PrivateNoteType::class);
        $privateNoteForm->handleRequest($request);

        if ($privateNoteForm->isSubmitted()) {
            $data = $privateNoteForm->getData();

            try {
                $this->dispatchCommand(new SetPrivateNoteAboutCustomerCommand(
                    $customerId,
                    $data['note']
                ));
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
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
     * @param int $customerId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index')]
    public function transformGuestToCustomerAction(
        int $customerId,
        Request $request,
        LegacyContext $legacyContext,
    ): RedirectResponse {
        try {
            $this->dispatchCommand(new TransformGuestToCustomerCommand($customerId));

            $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        if ($request->query->get('id_order')) {
            $legacyLink = $legacyContext->getAdminLink('AdminOrders', true, [
                'id_order' => $request->query->get('id_order'),
                'vieworder' => true,
            ]);

            return $this->redirect($legacyLink);
        }

        return $this->redirectToRoute('admin_customers_view', [
            'customerId' => $customerId,
        ]);
    }

    /**
     * Sets required fields for customer
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index')]
    public function setRequiredFieldsAction(Request $request): RedirectResponse
    {
        $requiredFieldsForm = $this->getRequiredFieldsForm();
        $requiredFieldsForm->handleRequest($request);

        if ($requiredFieldsForm->isSubmitted()) {
            $data = $requiredFieldsForm->getData();

            $this->dispatchCommand(new SetRequiredFieldsForCustomerCommand($data['required_fields']));

            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function searchAction(Request $request): JsonResponse
    {
        $query = $request->query->get('customer_search');
        $phrases = explode(' OR ', $query);
        $isRequestFromLegacyPage = !$request->query->has('sf2');

        if (!$request->query->has('shopId')) {
            // this is important for keeping backwards compatibility, null acts different compared to AllShops constraint.
            $shopConstraint = null;
        } else {
            $shopId = $request->query->getInt('shopId');
            $shopConstraint = $shopId ? ShopConstraint::shop($shopId) : ShopConstraint::allShops();
        }

        try {
            $customers = $this->dispatchQuery(new SearchCustomers(
                $phrases,
                $shopConstraint
            ));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

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
     * Provides customer information for address creation in json format
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function getCustomerInformationAction(Request $request): JsonResponse
    {
        try {
            $email = $request->query->get('email');

            /** @var AddressCreationCustomerInformation $customerInformation */
            $customerInformation = $this->dispatchQuery(new GetCustomerForAddressCreation($email));

            return $this->json($customerInformation);
        } catch (Exception $e) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($e instanceof CustomerException) {
                $code = Response::HTTP_NOT_FOUND;
            }

            return $this->json([
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ],
                $code
            );
        }
    }

    /**
     * Toggle customer status.
     *
     * @param int $customerId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to edit this.')]
    public function toggleStatusAction(int $customerId): JsonResponse
    {
        try {
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->dispatchQuery(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand((int) $customerId);
            $editCustomerCommand->setIsEnabled(!$editableCustomer->isEnabled());

            $this->dispatchCommand($editCustomerCommand);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
        } catch (CustomerException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ];
        }

        return $this->json($response);
    }

    /**
     * Toggle customer newsletter subscription status.
     *
     * @param int $customerId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to edit this.')]
    public function toggleNewsletterSubscriptionAction(int $customerId): JsonResponse
    {
        try {
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->dispatchQuery(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand($customerId);

            // toggle newsletter subscription
            $editCustomerCommand->setNewsletterSubscribed(!$editableCustomer->isNewsletterSubscribed());

            $this->dispatchCommand($editCustomerCommand);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
        } catch (CustomerException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ];
        }

        return $this->json($response);
    }

    /**
     * Toggle customer partner offer subscription status.
     *
     * @param int $customerId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to edit this.')]
    public function togglePartnerOfferSubscriptionAction(int $customerId): JsonResponse
    {
        try {
            /** @var EditableCustomer $editableCustomer */
            $editableCustomer = $this->dispatchQuery(new GetCustomerForEditing($customerId));

            $editCustomerCommand = new EditCustomerCommand($customerId);
            $editCustomerCommand->setIsPartnerOffersSubscribed(!$editableCustomer->isPartnerOffersSubscribed());

            $this->dispatchCommand($editCustomerCommand);

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
        } catch (CustomerException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ];
        }

        return $this->json($response);
    }

    /**
     * Delete customers in bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $form = $this->createForm(DeleteCustomersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $customerIds = array_map(function ($customerId) {
                return (int) $customerId;
            }, $data['customers_to_delete']);

            try {
                $command = new BulkDeleteCustomerCommand(
                    $customerIds,
                    $data['delete_method']
                );

                $this->dispatchCommand($command);

                $this->addFlash(
                    'success',
                    $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
                );
            } catch (CustomerException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Delete customer.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to delete this.')]
    public function deleteAction(Request $request): RedirectResponse
    {
        $form = $this->createForm(DeleteCustomersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $customerId = (int) reset($data['customers_to_delete']);

            try {
                $command = new DeleteCustomerCommand(
                    $customerId,
                    $data['delete_method']
                );

                $this->dispatchCommand($command);

                $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
            } catch (CustomerException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Enable customers in bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to edit this.')]
    public function enableBulkAction(Request $request): RedirectResponse
    {
        $customerIds = array_map(function ($customerId) {
            return (int) $customerId;
        }, $request->request->all('customer_customers_bulk'));

        try {
            $command = new BulkEnableCustomerCommand($customerIds);

            $this->dispatchCommand($command);

            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_customers_index');
    }

    /**
     * Disable customers in bulk action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customers_index', message: 'You do not have permission to edit this.')]
    public function disableBulkAction(Request $request): RedirectResponse
    {
        try {
            $customerIds = array_map(function ($customerId) {
                return (int) $customerId;
            }, $request->request->all('customer_customers_bulk'));

            $command = new BulkDisableCustomerCommand($customerIds);

            $this->dispatchCommand($command);

            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (CustomerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportAction(
        CustomerFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.customer')]
        GridFactoryInterface $customerGridFactory,
    ): CsvResponse {
        $filters = new CustomerFilters(['limit' => null] + $filters->all());
        $grid = $customerGridFactory->getGrid($filters);

        $headers = [
            'id_customer' => $this->trans('ID', [], 'Admin.Global'),
            'social_title' => $this->trans('Social title', [], 'Admin.Global'),
            'firstname' => $this->trans('First name', [], 'Admin.Global'),
            'lastname' => $this->trans('Last name', [], 'Admin.Global'),
            'email' => $this->trans('Email address', [], 'Admin.Global'),
            'default_group' => $this->trans('Group', [], 'Admin.Global'),
            'company' => $this->trans('Company', [], 'Admin.Global'),
            'total_spent' => $this->trans('Sales', [], 'Admin.Global'),
            'enabled' => $this->trans('Enabled', [], 'Admin.Global'),
            'newsletter' => $this->trans('Newsletter', [], 'Admin.Global'),
            'partner_offers' => $this->trans('Partner offers', [], 'Admin.Orderscustomers.Feature'),
            'registration' => $this->trans('Registration', [], 'Admin.Orderscustomers.Feature'),
            'connect' => $this->trans('Last visit', [], 'Admin.Orderscustomers.Feature'),
        ];

        $data = [];

        foreach ($grid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_customer' => $record['id_customer'],
                'social_title' => '--' === $record['social_title'] ? '' : $record['social_title'],
                'firstname' => $record['firstname'],
                'lastname' => $record['lastname'],
                'email' => $record['email'],
                'default_group' => $record['default_group'],
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
            ->setFileName('customer_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param int $customerId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function getCartsAction(int $customerId): JsonResponse
    {
        try {
            $carts = $this->dispatchQuery(new GetCustomerCarts($customerId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([
            'carts' => $carts,
        ]);
    }

    /**
     * @param int $customerId
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('create', 'AdminOrders')")]
    public function getOrdersAction(int $customerId): JsonResponse
    {
        try {
            $orders = $this->dispatchQuery(new GetCustomerOrders($customerId));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([
            'orders' => $orders,
        ]);
    }

    /**
     * @return FormInterface
     */
    private function getRequiredFieldsForm(): FormInterface
    {
        $requiredFields = $this->dispatchQuery(new GetRequiredFieldsForCustomer());

        return $this->createForm(RequiredFieldsType::class, ['required_fields' => $requiredFields]);
    }

    /**
     * If customer form is submitted and groups are not selected
     * we add empty groups to request
     *
     * @param Request $request
     */
    private function addGroupSelectionToRequest(Request $request): void
    {
        if (!$request->isMethod(Request::METHOD_POST)) {
            return;
        }

        if (!$request->request->has('customer')
            || isset($request->request->all('customer')['group_ids'])
        ) {
            return;
        }

        $customerData = $request->request->all('customer');
        $customerData['group_ids'] = [];

        $request->request->set('customer', $customerData);
    }

    /**
     * Get errors that can be used to translate exceptions into user friendly messages
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            CustomerNotFoundException::class => $this->trans(
                'This customer does not exist.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            DuplicateCustomerEmailException::class => [
                DuplicateCustomerEmailException::ADD => $this->trans(
                    'You can\'t create a registered customer with email "%s", because a registered customer with this email already exists.',
                    [$e instanceof DuplicateCustomerEmailException ? $e->getEmail()->getValue() : ''],
                    'Admin.Orderscustomers.Notification',
                ),
                DuplicateCustomerEmailException::EDIT => $this->trans(
                    'You can\'t update the email to "%s", because a registered customer with this email already exists.',
                    [$e instanceof DuplicateCustomerEmailException ? $e->getEmail()->getValue() : ''],
                    'Admin.Orderscustomers.Notification',
                ),
            ],
            CustomerDefaultGroupAccessException::class => $this->trans(
                'A default customer group must be selected in group box.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            CustomerByEmailNotFoundException::class => $this->trans(
                'This email address is not registered.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            CustomerConstraintException::class => [
                CustomerConstraintException::INVALID_PASSWORD => $this->trans(
                    'Password should be at least %length% characters long.',
                    ['%length%' => Password::MIN_LENGTH],
                    'Admin.Orderscustomers.Help',
                ),
                CustomerConstraintException::INVALID_FIRST_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('First name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                CustomerConstraintException::INVALID_LAST_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Last name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                CustomerConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Email', [], 'Admin.Global'))],
                    'Admin.Notifications.Error',
                ),
                CustomerConstraintException::INVALID_BIRTHDAY => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Birthday', [], 'Admin.Orderscustomers.Feature'))],
                    'Admin.Notifications.Error',
                ),
                CustomerConstraintException::INVALID_APE_CODE => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('APE', [], 'Admin.Orderscustomers.Feature'))],
                    'Admin.Notifications.Error',
                ),
            ],
            CustomerTransformationException::class => [
                CustomerTransformationException::CUSTOMER_IS_NOT_GUEST => $this->trans(
                    'This customer already exists as a non-guest.',
                    [],
                    'Admin.Orderscustomers.Notification',
                ),
                CustomerTransformationException::TRANSFORMATION_FAILED => $this->trans(
                    'An error occurred while updating customer information.',
                    [],
                    'Admin.Orderscustomers.Notification',
                ),
            ],
            MissingCustomerRequiredFieldsException::class => $this->trans(
                'The %s field is required.',
                [
                    implode(
                        ',',
                        $e instanceof MissingCustomerRequiredFieldsException ? $e->getMissingRequiredFields() : []
                    ),
                ],
                'Admin.Notifications.Error',
            ),
        ];
    }

    /**
     * Manage legacy flashes
     *
     * @todo Remove this code when legacy edit will be migrated.
     *
     * @param int $messageId The message id from legacy context
     */
    private function manageLegacyFlashes($messageId): void
    {
        $messages = [
            1 => $this->trans('Successful deletion', [], 'Admin.Notifications.Success'),
            4 => $this->trans('Update successful.', [], 'Admin.Notifications.Success'),
        ];

        if (isset($messages[$messageId])) {
            $this->addFlash(
                'success',
                $messages[$messageId]
            );
        }
    }

    /**
     * @return array
     */
    private function getCustomerIndexToolbarButtons(): array
    {
        $toolbarButtons = [];

        $isSingleShopContext = $this->getShopContext()->getShopConstraint()->isSingleShopContext();

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_customers_create'),
            'desc' => $this->trans('Add new customer', [], 'Admin.Orderscustomers.Feature'),
            'icon' => 'add_circle_outline',
            'disabled' => !$isSingleShopContext,
        ];

        if (!$isSingleShopContext) {
            $toolbarButtons['add']['help'] = $this->trans(
                'You can use this feature in a single-store context only. Switch contexts to enable it.',
                [],
                'Admin.Orderscustomers.Feature'
            );
            $toolbarButtons['add']['href'] = '#';
        }

        return $toolbarButtons;
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    private function getCustomerViewToolbarButtons(int $customerId): array
    {
        $toolbarButtons = [];

        $toolbarButtons['edit'] = [
            'href' => $this->generateUrl('admin_customers_edit', ['customerId' => $customerId]),
            'desc' => $this->trans('Edit customer', [], 'Admin.Orderscustomers.Feature'),
            'icon' => 'mode_edit',
        ];

        return $toolbarButtons;
    }
}
