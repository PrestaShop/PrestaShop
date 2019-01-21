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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\ContactFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ContactsController is responsible for actions and rendering
 * of "Shop Parameters > Contact > Contacts" page.
 */
class ContactsController extends FrameworkBundleAdminController
{
    /**
     * Shows page content.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/index.html.twig")
     *
     * @param Request $request
     * @param ContactFilters $filters
     *
     * @return array
     */
    public function indexAction(Request $request, ContactFilters $filters)
    {
        $contactsGridFactory = $this->get('prestashop.core.grid.factory.contacts');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        $contactsGrid = $contactsGridFactory->getGrid($filters);

        return [
            'layoutTitle' => $this->trans('Contacts', 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_contacts_create'),
                    'desc' => $this->trans('Add new contact', 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'grid' => $gridPresenter->present($contactsGrid),
        ];
    }

    /**
     * Grid search action.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $gridDefinitionFactory = $this->get('prestashop.core.grid.definition.factory.contacts');
        $contactsGridDefinition = $gridDefinitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $filtersForm = $gridFilterFormFactory->create($contactsGridDefinition);
        $filtersForm->handleRequest($request);

        $filters = [];

        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }

        return $this->redirectToRoute('admin_contacts_index', ['filters' => $filters]);
    }

    /**
     * Display the Contact creation form.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to add this.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $contactFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.contact_form_builder');
        $contactForm = $contactFormBuilder->getForm();
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted()) {
            $contactFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.contact_form_handler');
            $contactFormHandler->handle($contactForm);

            $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_contacts_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/add.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }

    /**
     * Display the contact edit form.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $contactId
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($contactId, Request $request)
    {
        $contactFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.contact_form_builder');
        $contactForm = $contactFormBuilder->getFormFor((int) $contactId);

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted()) {
            $contactFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.contact_form_handler');
            $result = $contactFormHandler->handleFor((int) $contactId, $contactForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_contacts_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/edit.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }

    /**
     * Delete a contact.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     * @DemoRestricted(redirectRoute="admin_contacts")
     *
     * @param int $contactId
     *
     * @return RedirectResponse
     */
    public function deleteAction($contactId)
    {
        $contactDeleter = $this->get('prestashop.adapter.contact.deleter');

        if ($errors = $contactDeleter->delete([$contactId])) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_contacts_index');
    }

    /**
     * Bulk delete contacts.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     * @DemoRestricted(redirectRoute="admin_contacts")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $contactIds = $request->request->get('contact_bulk');
        $contactDeleter = $this->get('prestashop.adapter.contact.deleter');

        if ($errors = $contactDeleter->delete($contactIds)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_contacts_index');
    }
}
