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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Command\BulkDeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\DeleteAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Exception\TranslatableCoreException;
use PrestaShop\PrestaShop\Core\Search\Filters\AttributeGroupFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AttributeGroupController extends FrameworkBundleAdminController
{
    /**
     * Displays Attribute groups page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param AttributeGroupFilters $attributeGroupFilters
     *
     * @return Response
     */
    public function indexAction(Request $request, AttributeGroupFilters $attributeGroupFilters)
    {
        $attributeGroupGridFactory = $this->get('prestashop.core.grid.factory.attribute_group');
        $attributeGroupGrid = $attributeGroupGridFactory->getGrid($attributeGroupFilters);

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $this->getContext()->employee->id, ShowcaseCard::ATTRIBUTES_CARD)
        );

        return $this->render('@PrestaShop/Admin/Sell/Catalog/AttributeGroup/index.html.twig', [
            'attributeGroupGrid' => $this->presentGrid($attributeGroupGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'showcaseCardName' => ShowcaseCard::ATTRIBUTES_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClosed,
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     message="You do not have permission to create this."
     * )
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        //@todo: implement in antoher pr
        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     *
     * @param int $attributeGroupId
     *
     * @return RedirectResponse
     */
    public function editAction(int $attributeGroupId)
    {
        //@todo: implement in antoher pr
        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="You do not have permission to export this."
     * )

     *
     * @param int $attributeGroupId
     *
     * @return RedirectResponse
     */
    public function exportAction(int $attributeGroupId)
    {
        //@todo: implement in antoher pr
        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * Updates attribute groups positioning order
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attribute_groups_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updatePositionAction(Request $request)
    {
        $positionsData = [
            'positions' => $request->request->get('positions'),
        ];

        $positionDefinition = $this->get('prestashop.core.grid.attribute_group.position_definition');
        $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (TranslatableCoreException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        } catch (Exception $e) {
            $this->flashErrors([$e->getMessage()]);
        }

        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * Deletes attribute group
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attribute_groups_index",
     * )
     *
     * @param int $attributeGroupId
     *
     * @return RedirectResponse
     */
    public function deleteAction($attributeGroupId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAttributeGroupCommand((int) $attributeGroupId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * Deletes multiple attribute groups by provided ids from request
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attribute_groups_index",
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteAttributeGroupCommand(
                    $this->getAttributeGroupIdsFromRequest($request))
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getAttributeGroupIdsFromRequest(Request $request)
    {
        $attributeGroupIds = $request->request->get('attribute_group_bulk');

        if (!is_array($attributeGroupIds)) {
            return [];
        }

        foreach ($attributeGroupIds as $i => $attributeGroupId) {
            $attributeGroupIds[$i] = (int) $attributeGroupId;
        }

        return $attributeGroupIds;
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            AttributeGroupNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            DeleteAttributeGroupException::class => [
                DeleteAttributeGroupException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteAttributeGroupException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
