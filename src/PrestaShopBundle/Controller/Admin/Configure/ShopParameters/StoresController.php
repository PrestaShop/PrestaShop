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
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\StoreFilters;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkToggleStoreStatusCommand;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject;


/**
 * StoresController is responsible for actions and rendering
 * of "Shop Parameters > Contact > Stores" page.
 */
class StoresController extends FrameworkBundleAdminController
{
    /**
     * Shows page content.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param StoreFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, StoreFilters $filters)
    {
        $storeGridFactory = $this->get('prestashop.core.grid.factory.stores');
        $storeGrid = $storeGridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Stores', 'Admin.Navigation.Menu'),
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'desc' => $this->trans('Add new store', 'Admin.Shopparameters.Feature'),
                        'icon' => 'add_circle_outline',
                        'href' => $this->generateUrl('admin_stores_create'),
                    ],
                ],
                'storeGrid' => $this->presentGrid($storeGrid),
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
        $gridDefinitionFactory = $this->get('prestashop.core.grid.definition.factory.stores');
        $storesGridDefinition = $gridDefinitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $filtersForm = $gridFilterFormFactory->create($storesGridDefinition);
        $filtersForm->handleRequest($request);

        $filters = [];

        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }

        return $this->redirectToRoute('admin_stores_index', ['filters' => $filters]);
    }

    /**
     * Display the Store creation form.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You do not have permission to add this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $storeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.stores_form_builder');
        $storeForm = $storeFormBuilder->getForm();
        $storeForm->handleRequest($request);

        try {
            $storeFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.stores_form_handler');
            $result = $storeFormHandler->handle($storeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_stores_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeForm' => $storeForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Display the store edit form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $storeId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($storeId, Request $request)
    {
        $storeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.stores_form_builder');
        $storeForm = $storeFormBuilder->getFormFor((int) $storeId);

        $storeForm->handleRequest($request);

        try {
            $storeFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.store_form_handler');
            $result = $storeFormHandler->handleFor((int) $storeId, $storeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_stores_index');
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages($exception))
            );
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Contact/Stores/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'storeForm' => $storeForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Delete a store.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_stores_index")
     *
     * @param int $storeId
     *
     * @return RedirectResponse
     */
    public function deleteAction($storeId)
    {
        $storeDeleter = $this->get('prestashop.adapter.stores.deleter');

        if ($errors = $storeDeleter->delete([$storeId])) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_stores_index');
    }

    /**
     * Bulk delete stores.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_stores_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $storeIds = $request->request->get('store_bulk');
        $storeDeleter = $this->get('prestashop.adapter.stores.deleter');

        if ($errors = $storeDeleter->delete($storeIds)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_stores_index');
    }
    
    /**
     * Toggles store status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     *     message="You need permission to edit this."
     * )
     *
     * @DemoRestricted(redirectRoute="admin_stores_index")
     *
     * @param int $storeId
     *
     * @return JsonResponse
     */
    public function toggleStatusAction(int $storeId): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new ToggleStoreStatusCommand($storeId));
    
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (StoreException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ];
        }
    
        return $this->json($response);
    }
    
    /**
     * Enables store status on bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     * )
     * @DemoRestricted(redirectRoute="admin_stores_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableStatusAction(Request $request): RedirectResponse
    {
        $storeIds = $this->getStoreIdsFromRequest($request);
    
        try {
            $this->getCommandBus()->handle(new BulkToggleStoreStatusCommand($storeIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (StoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }
    
        return $this->redirectToRoute('admin_stores_index');
    }
    
    /**
     * Disables store status on bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_stores_index",
     * )
     * @DemoRestricted(redirectRoute="admin_stores_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableStatusAction(Request $request): RedirectResponse
    {
        $storeIds = $this->getStoreIdsFromRequest($request);
    
        try {
            $this->getCommandBus()->handle(new BulkToggleStoreStatusCommand($storeIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (StoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }
    
        return $this->redirectToRoute('admin_stores_index');
    }
    
    /**
     * Get store IDs from request for bulk actions.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getStoreIdsFromRequest(Request $request): array
    {
        $storeIds = $request->request->get('store_bulk');
    
        if (!is_array($storeIds)) {
            return [];
        }
    
        foreach ($storeIds as $i => $storeId) {
            $storeIds[$i] = (int) $storeId;
        }
    
        return $storeIds;
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e)
    {
        return [
            StoreNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            StoreConstraintException::class => [
                StoreConstraintException::INVALID_SHOP_ASSOCIATION => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Shop association', 'Admin.Global')
                        ),
                    ]
                ),
                StoreConstraintException::INVALID_TITLE => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf(
                            '"%s"',
                            $this->trans('Title', 'Admin.Global')
                        ),
                    ]
                ),
                StoreConstraintException::MISSING_TITLE_FOR_DEFAULT_LANGUAGE => $this->trans(
                    'The field %field_name% is required at least in your default language.',
                    'Admin.Notifications.Error',
                    [
                        '%field_name%' => $this->trans('Title', 'Admin.Global'),
                    ]
                ),
                StoreConstraintException::INVALID_DESCRIPTION => $this->trans(
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
