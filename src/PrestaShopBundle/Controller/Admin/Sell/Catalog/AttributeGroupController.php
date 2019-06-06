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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\BulkDeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\DeleteAttributeGroupException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AttributeGroupGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\AttributeGroupFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
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
     * @param AttributeGroupFilters $attributeGroupFilters
     *
     * @return Response
     */
    public function indexAction(AttributeGroupFilters $attributeGroupFilters)
    {
        $attributeGroupGridFactory = $this->get('prestashop.core.grid.factory.attribute_group');
        $attributeGroupGrid = $attributeGroupGridFactory->getGrid($attributeGroupFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/AttributeGroup/index.html.twig', [
            'attributeGroupGrid' => $this->presentGrid($attributeGroupGrid),
        ]);
    }

    /**
     * Responsible for grid filtering
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attribute_groups_index",
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
            $this->get('prestashop.core.grid.definition.factory.attribute_group'),
            $request,
            AttributeGroupGridDefinitionFactory::GRID_ID,
            'admin_attribute_groups_index'
        );
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
        } catch (AttributeGroupException $e) {
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
        } catch (AttributeGroupException $e) {
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
