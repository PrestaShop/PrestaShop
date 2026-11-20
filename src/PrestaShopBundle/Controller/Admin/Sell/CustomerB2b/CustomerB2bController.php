<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerB2b;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\MissingCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\PasswordSetupLinkNotSentException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerB2bController manages the "Sell > Customers B2B" page.
 */
class CustomerB2bController extends PrestaShopAdminController
{
    /**
     * Lists the B2B customers. The grid itself lands with the customer/entity link management.
     */
    #[AdminSecurity("is_granted('read', 'AdminCustomersB2B')")]
    public function listAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/CustomerB2b/list.html.twig', [
            'layoutHeaderToolbarBtn' => $this->getIndexToolbarButtons(),
        ]);
    }

    #[AdminSecurity(
        data: "is_granted('create', 'AdminCustomersB2B') && is_granted('create', 'AdminCustomers')",
        message: 'You do not have permission to create this.',
        redirectRoute: 'admin_customer_b2b_list',
    )]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.customer_b2b_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.customer_b2b_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        /* @see AddCustomerHandler may not handle other context types correctly */
        if (!$this->getShopContext()->isSingleShopContext()) {
            return $this->redirectToRoute('admin_customer_b2b_list');
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);
            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customer_b2b_list');
            }
        } catch (PasswordSetupLinkNotSentException) {
            $this->addFlash('warning', $this->trans('The B2B customer was created, but the password reset email could not be sent.', [], 'Admin.Orderscustomers.Notification'));

            return $this->redirectToRoute('admin_customer_b2b_list');
        } catch (Exception $e) {
            $message = $this->getErrorMessageForException($e, $this->getErrorMessages($e));
            if (!$this->mapFormError($form, $e, $message)) {
                $this->addFlash('error', $message);
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerB2b/create.html.twig', [
            'form' => $form,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminCustomersB2B'),
            'layoutTitle' => $this->trans('Add new B2B customer', [], 'Admin.Navigation.Menu'),
        ]);
    }

    private function getIndexToolbarButtons(): array
    {
        $toolbarButtons = [
            'add' => [
                'href' => $this->generateUrl('admin_customer_b2b_create'),
                'desc' => $this->trans('Add new B2B customer', [], 'Admin.Navigation.Menu'),
                'icon' => 'add_circle_outline',
            ],
        ];

        if (!$this->getShopContext()->isSingleShopContext()) {
            $toolbarButtons['add']['disabled'] = true;
            $toolbarButtons['add']['help'] = $this->trans('You can use this feature in a single-store context only. Switch contexts to enable it.', [], 'Admin.Orderscustomers.Feature');
            $toolbarButtons['add']['href'] = '#';
        }

        return $toolbarButtons;
    }

    /**
     * @return bool whether the error was mapped to the form
     */
    private function mapFormError(FormInterface $form, Exception $e, string $message): bool
    {
        $path = match (true) {
            $e instanceof DuplicateCustomerEmailException => 'customer.email',
            $e instanceof CustomerB2bAlreadyExistsException,
            $e instanceof CannotAddCustomerB2bException && CannotAddCustomerB2bException::DELETED_CUSTOMER === $e->getCode() => 'existing_customer',
            default => null,
        };

        if (null === $path) {
            return false;
        }

        foreach (explode('.', $path) as $name) {
            if (!$form->has($name)) {
                return false;
            }

            $form = $form->get($name);
        }

        $form->addError(new FormError($message));

        return true;
    }

    private function getErrorMessages(Exception $e): array
    {
        return [
            B2bRoleNotFoundException::class => $this->trans('The selected role does not exist.', [], 'Admin.Notifications.Error'),
            BusinessEntityNotFoundException::class => $this->trans('The selected business entity does not exist.', [], 'Admin.Notifications.Error'),
            CannotAddCustomerB2bException::class => [
                CannotAddCustomerB2bException::FAILED_SAVE => $this->trans('An error occurred while creating the object.', [], 'Admin.Notifications.Error'),
                CannotAddCustomerB2bException::GUEST_CUSTOMER => $this->trans('The selected customer is a guest.', [], 'Admin.Notifications.Error'),
                CannotAddCustomerB2bException::DELETED_CUSTOMER => $this->trans('The selected customer is deleted.', [], 'Admin.Notifications.Error'),
                CannotAddCustomerB2bException::SHOP_MISMATCH => $this->trans('Customer and business entity belong to different shops.', [], 'Admin.Notifications.Error'),
            ],
            CustomerB2bAlreadyExistsException::class => $this->trans('This customer already has a B2B profile.', [], 'Admin.Notifications.Error'),
            CustomerB2bConstraintException::class => [
                CustomerB2bConstraintException::INVALID_EXTERNAL_REF => $this->trans('The %s field is invalid.', [
                    \sprintf('"%s"', $this->trans('External Reference', [], 'Admin.Global')),
                ], 'Admin.Notifications.Error'),
            ],
            CustomerConstraintException::class => [
                CustomerConstraintException::INVALID_FIRST_NAME => $this->trans('The %s field is invalid.', [
                    \sprintf('"%s"', $this->trans('First name', [], 'Admin.Global')),
                ], 'Admin.Notifications.Error'),
                CustomerConstraintException::INVALID_LAST_NAME => $this->trans('The %s field is invalid.', [
                    \sprintf('"%s"', $this->trans('Last name', [], 'Admin.Global')),
                ], 'Admin.Notifications.Error'),
            ],
            CustomerNotFoundException::class => $this->trans('This customer does not exist.', [], 'Admin.Orderscustomers.Notification'),
            DuplicateCustomerEmailException::class => [
                DuplicateCustomerEmailException::ADD => $this->trans('You can\'t create a registered customer with email "%s", because a registered customer with this email already exists.', [
                    $e instanceof DuplicateCustomerEmailException ? $e->getEmail()->getValue() : '',
                ], 'Admin.Orderscustomers.Notification'),
            ],
            MissingCustomerRequiredFieldsException::class => $this->trans('The %s field is required.', [
                implode(',', $e instanceof MissingCustomerRequiredFieldsException ? $e->getMissingRequiredFields() : []),
            ], 'Admin.Notifications.Error'),
        ];
    }
}
