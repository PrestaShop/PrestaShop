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
use PrestaShop\PrestaShop\Adapter\Support\ContactDeleter;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ContactFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ContactsController is responsible for actions and rendering
 * of "Shop Parameters > Contact > Contacts" page.
 */
class ContactsController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ContactFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.contacts')]
        GridFactoryInterface $contactGridFactory,
    ): Response {
        $contactGrid = $contactGridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Contacts', [], 'Admin.Navigation.Menu'),
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'desc' => $this->trans('Add new contact', [], 'Admin.Shopparameters.Feature'),
                        'icon' => 'add_circle_outline',
                        'href' => $this->generateUrl('admin_contacts_create'),
                    ],
                ],
                'contactGrid' => $this->presentGrid($contactGrid),
            ]
        );
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to add this.', redirectRoute: 'admin_contacts_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.contact_form_builder')]
        FormBuilderInterface $contactFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.contact_form_handler')]
        FormHandlerInterface $contactFormHandler,
    ): Response {
        $contactForm = $contactFormBuilder->getForm();
        $contactForm->handleRequest($request);

        try {
            $result = $contactFormHandler->handle($contactForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_contacts_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contactForm' => $contactForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New contact', [], 'Admin.Navigation.Menu'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_contacts_index')]
    public function editAction(
        int $contactId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.contact_form_builder')]
        FormBuilderInterface $contactFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.contact_form_handler')]
        FormHandlerInterface $contactFormHandler,
    ): Response {
        $contactForm = $contactFormBuilder->getFormFor((int) $contactId);

        $contactForm->handleRequest($request);

        try {
            $result = $contactFormHandler->handleFor((int) $contactId, $contactForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_contacts_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Contacts/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'contactForm' => $contactForm->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing %name%',
                [
                    '%name%' => $contactForm->getData()['title'][$this->getLanguageContext()->getId()],
                ],
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_contacts_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to delete this.', redirectRoute: 'admin_contacts_index')]
    public function deleteAction(
        int $contactId,
        ContactDeleter $contactDeleter,
    ): RedirectResponse {
        if ($errors = $contactDeleter->delete([$contactId])) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_contacts_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_contacts_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_contacts_index', message: 'You do not have permission to delete this.')]
    public function deleteBulkAction(
        Request $request,
        ContactDeleter $contactDeleter,
    ): RedirectResponse {
        $contactIds = $request->request->all('contact_bulk');

        if ($errors = $contactDeleter->delete($contactIds)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_contacts_index');
    }

    private function getErrorMessages(): array
    {
        return [
            ContactNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            ContactConstraintException::class => [
                ContactConstraintException::INVALID_SHOP_ASSOCIATION => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Store association', [], 'Admin.Global')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
                ContactConstraintException::INVALID_TITLE => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Title', [], 'Admin.Global')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
                ContactConstraintException::MISSING_TITLE_FOR_DEFAULT_LANGUAGE => $this->trans(
                    'The field %field_name% is required at least in your default language.',
                    [
                        '%field_name%' => $this->trans('Title', [], 'Admin.Global'),
                    ],
                    'Admin.Notifications.Error',
                ),
                ContactConstraintException::INVALID_DESCRIPTION => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Description', [], 'Admin.Global')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
            ],
            DomainConstraintException::class => [
                DomainConstraintException::INVALID_EMAIL => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Email address', [], 'Admin.Global')
                        ),
                    ],
                    'Admin.Notifications.Error',
                ),
            ],
        ];
    }
}
