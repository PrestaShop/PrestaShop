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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\ContactFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
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
     * @param Request $request
     * @param ContactFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, ContactFilters $filters)
    {
        $contactGridFactory = $this->get('prestashop.core.grid.factory.contacts');
        $contactGrid = $contactGridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Contacts', 'Admin.Navigation.Menu'),
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'desc' => $this->trans('Add new contact', 'Admin.Shopparameters.Feature'),
                        'icon' => 'add_circle_outline',
                        'href' => $this->generateUrl('admin_contacts_create'),
                    ],
                ],
                'contactGrid' => $this->presentGrid($contactGrid),
            ]
        );
    }

    /**
     * @deprecated since 8.0 and will be removed in next major. Use CommonController:searchGridAction instead
     *
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
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_contacts_index",
     *     message="You do not have permission to add this."
     * )
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

        try {
            $contactFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.contact_form_handler');
            $result = $contactFormHandler->handle($contactForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_contacts_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contactForm' => $contactForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Display the contact edit form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_contacts_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $contactId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($contactId, Request $request)
    {
        $contactFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.contact_form_builder');
        $contactForm = $contactFormBuilder->getFormFor((int) $contactId);

        $contactForm->handleRequest($request);

        try {
            $contactFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.contact_form_handler');
            $result = $contactFormHandler->handleFor((int) $contactId, $contactForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_contacts_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contactForm' => $contactForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Delete a contact.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_contacts_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_contacts_index")
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
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_contacts_index');
    }

    /**
     * Bulk delete contacts.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_contacts_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_contacts_index")
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

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        return [
            ContactNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            ContactConstraintException::class => [
                ContactConstraintException::INVALID_SHOP_ASSOCIATION => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Store association', 'Admin.Global')
                        ),
                    ]
                ),
                ContactConstraintException::INVALID_TITLE => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Title', 'Admin.Global')
                        ),
                    ]
                ),
                ContactConstraintException::MISSING_TITLE_FOR_DEFAULT_LANGUAGE => $this->trans(
                    'The field %field_name% is required at least in your default language.',
                    'Admin.Notifications.Error',
                    [
                        '%field_name%' => $this->trans('Title', 'Admin.Global'),
                    ]
                ),
                ContactConstraintException::INVALID_DESCRIPTION => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Description', 'Admin.Global')
                        ),
                    ]
                ),
            ],
            DomainConstraintException::class => [
                DomainConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Email address', 'Admin.Global')
                        ),
                    ]
                ),
            ],
        ];
    }
}
