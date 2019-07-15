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

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Command\BulkDeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Command\DeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\DeleteAttributeException;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AttributeGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\AttributeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Attributes & Features > Attributes > Attribute
 */
class AttributeController extends FrameworkBundleAdminController
{
    /**
     * Displays Attribute groups > attributes page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attributes_index",
     *     redirectQueryParamsToKeep={"attributeGroupId"}
     * )
     *
     * @param Request $request
     * @param $attributeGroupId
     * @param AttributeFilters $attributeFilters
     *
     * @return Response
     */
    public function indexAction(Request $request, $attributeGroupId, AttributeFilters $attributeFilters)
    {
        try {
            $attributeGridFactory = $this->get('prestashop.core.grid.factory.attribute');
            $attributeGrid = $attributeGridFactory->getGrid($attributeFilters);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_attribute_groups_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attribute/index.html.twig', [
            'attributeGrid' => $this->presentGrid($attributeGrid),
            'attributeGroupId' => $attributeGroupId,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    public function createAction($attributeGroupId)
    {
        // @todo: implement in another pr
        return $this->redirectToRoute('admin_attributes_index', [
            'attributeGroupId' => $attributeGroupId,
        ]);
    }

    public function editAction($attributeId, $attributeGroupId)
    {
        // @todo: implement in another pr
        return $this->redirectToRoute('admin_attributes_index', [
            'attributeGroupId' => $attributeGroupId,
        ]);
    }

    /**
     * Responsible for grid filtering
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attributes_index",
     *     redirectQueryParamsToKeep={"attributeGroupId"}
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
            $this->get('prestashop.core.grid.definition.factory.attribute'),
            $request,
            AttributeGridDefinitionFactory::GRID_ID,
            'admin_attributes_index',
            ['attributeGroupId']
        );
    }

    /**
     * Deletes attribute
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attributes_index",
     *     redirectQueryParamsToKeep={"attributeGroupId"}
     * )
     *
     * @param int $attributeGroupId
     * @param int $attributeId
     *
     * @return RedirectResponse
     */
    public function deleteAction($attributeGroupId, $attributeId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAttributeCommand((int) $attributeId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attributes_index', [
            'attributeGroupId' => $attributeGroupId,
        ]);
    }

    /**
     * Deletes multiple attributes by provided ids from request
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attributes_index",
     *     redirectQueryParamsToKeep={"attributeGroupId"}
     * )
     *
     * @param int $attributeGroupId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction($attributeGroupId, Request $request)
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteAttributeCommand(
                $this->getAttributeIdsFromRequest($request))
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attributes_index', [
            'attributeGroupId' => $attributeGroupId,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getAttributeIdsFromRequest(Request $request)
    {
        $attributeIds = $request->request->get('attribute_bulk');

        if (!is_array($attributeIds)) {
            return [];
        }

        foreach ($attributeIds as $i => $attributeId) {
            $attributeIds[$i] = (int) $attributeId;
        }

        return $attributeIds;
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $notFoundMessage = $this->trans(
            'The object cannot be loaded (or found)',
            'Admin.Notifications.Error'
        );

        return [
            AttributeNotFoundException::class => $notFoundMessage,
            AttributeGroupNotFoundException::class => $notFoundMessage,
            DeleteAttributeException::class => [
                DeleteAttributeException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteAttributeException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
