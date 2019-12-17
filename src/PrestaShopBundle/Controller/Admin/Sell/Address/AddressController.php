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

namespace PrestaShopBundle\Controller\Admin\Sell\Address;

use Exception;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\SetRequiredFieldsForAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\BulkDeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotSetRequiredFieldsForAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerByEmailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\AddressFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Address\RequiredFieldsAddressType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class manages "Sell > Customers > Addresses" page.
 */
class AddressController extends FrameworkBundleAdminController
{
    /**
     * Show addresses listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param AddressFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, AddressFilters $filters): Response
    {
        $addressGridFactory = $this->get('prestashop.core.grid.grid_factory.address');
        $addressGrid = $addressGridFactory->getGrid($filters);
        $requiredFieldsForm = $this->getRequiredFieldsForm();

        return $this->render('@PrestaShop/Admin/Sell/Address/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'addressGrid' => $this->presentGrid($addressGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getAddressToolbarButtons(),
            'requiredFieldsForm' => $requiredFieldsForm->createView(),
        ]);
    }

    /**
     * Process addresses required fields configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_addresses_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function saveRequiredFieldsAction(Request $request): RedirectResponse
    {
        $addressRequiredFieldsForm = $this->getRequiredFieldsForm();
        $addressRequiredFieldsForm->handleRequest($request);

        if ($addressRequiredFieldsForm->isSubmitted() && $addressRequiredFieldsForm->isValid()) {
            $data = $addressRequiredFieldsForm->getData();

            try {
                $this->getCommandBus()->handle(new SetRequiredFieldsForAddressCommand($data['required_fields']));
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } catch (Exception $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->redirectToRoute('admin_addresses_index');
    }

    /**
     * Deletes address
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_addresses_index")
     *
     * @param int $addressId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $addressId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteAddressCommand($addressId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_addresses_index');
    }

    /**
     * Delete addresses in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_addresses_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $addressIds = $this->getBulkAddressesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteAddressCommand($addressIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_addresses_index');
    }

    /**
     * Responsible for grid filtering
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_addresses_index",
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.address'),
            $request,
            AddressGridDefinitionFactory::GRID_ID,
            'admin_addresses_index'
        );
    }

    /**
     * @return array
     */
    private function getAddressToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_addresses_create'),
            'desc' => $this->trans('Add new address', 'Admin.Orderscustomers.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * @return FormInterface
     */
    private function getRequiredFieldsForm(): FormInterface
    {
        $requiredFields = $this->getQueryBus()->handle(new GetRequiredFieldsForAddress());

        return $this->createForm(RequiredFieldsAddressType::class, ['required_fields' => $requiredFields]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkAddressesFromRequest(Request $request): array
    {
        $addressIds = $request->request->get('address_addresses_bulk');

        if (!is_array($addressIds)) {
            return [];
        }

        foreach ($addressIds as $i => $addressId) {
            $addressIds[$i] = (int) $addressId;
        }

        return $addressIds;
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_addresses_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $addressFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.address_form_builder'
        );

        $addressFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.address_form_handler'
        );

        $formData = [];
        $customerInfo = null;
        $customerId = null;
        if ($request->request->has('customer_address')) {
            if (isset($request->request->get('customer_address')['id_country'])) {
                $formCountryId = (int) $request->request->get('customer_address')['id_country'];
                $formData['id_country'] = $formCountryId;
            }
            if (isset($request->request->get('customer_address')['id_customer'])) {
                $idCustomer = (int) $request->request->get('customer_address')['id_customer'];
                $formData['id_customer'] = $idCustomer;
            }
        }

        if (empty($formData['id_customer']) && $request->query->has('id_customer')) {
            $formData['id_customer'] = (int) $request->query->get('id_customer');
        }

        if (!empty($formData['id_customer'])) {
            /** @var CustomerDataProvider $customerDataProvider */
            $customerDataProvider = $this->get('prestashop.adapter.data_provider.customer');
            $customerId = $formData['id_customer'];
            $customer = $customerDataProvider->getCustomer($customerId);
            $formData['first_name'] = $customer->firstname;
            $formData['last_name'] = $customer->lastname;
            $formData['company'] = $customer->company;
            $customerInfo = $customer->firstname . ' ' . $customer->lastname . ' (' . $customer->email . ')';
        }

        $addressForm = $addressFormBuilder->getForm($formData);
        $addressForm->handleRequest($request);

        try {
            $handlerResult = $addressFormHandler->handle($addressForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_addresses_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_addresses_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Address/add.html.twig', [
            'customerId' => $customerId,
            'customerInformation' => $customerInfo,
            'enableSidebar' => true,
            'addressForm' => $addressForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Handles edit form rendering and submission
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_addresses_index"
     * )
     *
     * @param int $addressId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $addressId, Request $request): Response
    {
        try {
            /** @var EditableCustomerAddress $editableAddress */
            $editableAddress = $this->getQueryBus()->handle(new GetCustomerAddressForEditing((int) $addressId));

            $addressFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.address_form_builder'
            );
            $addressFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.address_form_handler'
            );

            $formData = [];
            if ($request->request->has('customer_address') && isset($request->request->get('customer_address')['id_country'])) {
                $formCountryId = (int) $request->request->get('customer_address')['id_country'];
                $formData['id_country'] = $formCountryId;
            }

            $addressForm = $addressFormBuilder->getFormFor($addressId, $formData);
            $addressForm->handleRequest($request);
            $result = $addressFormHandler->handleFor($addressId, $addressForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_addresses_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_addresses_index');
        }

        $customerInfo = $editableAddress->getLastName() . ' ' .
            $editableAddress->getFirstName() . ' (' .
            $editableAddress->getCustomerEmail() . ')';

        return $this->render('@PrestaShop/Admin/Sell/Address/edit.html.twig', [
            'enableSidebar' => true,
            'customerId' => $editableAddress->getCustomerId()->getValue(),
            'customerInformation' => $customerInfo,
            'layoutTitle' => $this->trans('Edit', 'Admin.Actions'),
            'addressForm' => $addressForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            DeleteAddressException::class => [
                DeleteAddressException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
            ],
            BulkDeleteAddressException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof BulkDeleteAddressException ? $e->getAddressIds() : ''
            ),
            AddressNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotSetRequiredFieldsForAddressException::class => $this->trans(
                'An error occurred when attempting to update the required fields.',
                'Admin.Notifications.Error'
            ),
            AddressException::class => sprintf(
                $this->trans(
                    'Internal error #%s',
                    'Admin.Notifications.Error'
                ),
                $e->getMessage()
            ),
            InvalidAddressRequiredFieldsException::class => $this->trans(
                'Invalid data supplied.',
                'Admin.Notifications.Error'
            ),
            AddressConstraintException::class => [
                AddressConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
                AddressConstraintException::INVALID_FIELDS => $this->trans(
                    'An error occurred when attempting to update the required fields.',
                    'Admin.Notifications.Error'
                ),
            ],
            StateConstraintException::class => [
                StateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotUpdateAddressException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CannotAddAddressException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CustomerException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                'Admin.Notifications.Error'
            ),
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
            CustomerNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CustomerByEmailNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
