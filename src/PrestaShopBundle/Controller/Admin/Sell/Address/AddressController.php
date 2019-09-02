<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\SetRequiredFieldsForAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotSetRequiredFieldsForAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;
use PrestaShop\PrestaShop\Core\Search\Filters\AddressFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Address\RequiredFieldsAddressType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
            } catch (AddressException $e) {
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
        } catch (AddressException $e) {
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
        } catch (AddressException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_addresses_index');
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
                DeleteAddressException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
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
        ];
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
     * @return Response
     */
    public function createAction(): Response
    {
        $link = $this->getAdminLink('AdminAddresses', ['addaddress' => 1]);

        return $this->redirect($link);
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
     *
     * @return Response
     */
    public function editAction(int $addressId): Response
    {
        $link = $this->getAdminLink('AdminAddresses', ['id_address' => $addressId, 'updateaddress' => 1]);

        return $this->redirect($link);
    }
}
