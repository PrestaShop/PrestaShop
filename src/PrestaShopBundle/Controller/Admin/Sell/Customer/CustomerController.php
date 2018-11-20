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

use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Form\Admin\Sell\Customer\PrivateNoteType;
use PrestaShopBundle\Form\Admin\Sell\Customer\TransferGuestAccountType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
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
        $customerGridFactory = $this->get('prestashop.core.grid.factory.customer');
        $customerGrid = $customerGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Customer/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerGrid' => $this->presentGrid($customerGrid),
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
            $customerInformation = $this->getQueryBus()->handle(new GetCustomerInformation(new CustomerId($customerId)));
        } catch (CustomerNotFoundException $e) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_customers_index');
        }

        $transferGuestAccountForm = null;
        if ($customerInformation->getPersonalInformation()->isGuest()) {
            $transferGuestAccountForm = $this->createForm(TransferGuestAccountType::class, [
                'id_customer' => $customerId
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
}
