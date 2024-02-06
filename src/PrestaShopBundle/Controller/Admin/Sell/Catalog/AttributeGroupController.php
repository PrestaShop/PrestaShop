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
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\BulkDeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\CannotAddAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\DeleteAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Exception\TranslatableCoreException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\AttributeGroupFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AttributeGroupController extends FrameworkBundleAdminController
{
    /**
     * Displays Attribute groups page
     *
     * @param Request $request
     * @param AttributeGroupFilters $attributeGroupFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
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
            'layoutTitle' => $this->trans('Attributes', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to create this.')]
    public function createAction(Request $request): Response
    {
        $attributeGroupFormBuilder = $this->get('PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\AttributeGroupFormBuilder');
        $attributeFormHandler = $this->get('PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\AttributeGroupFormHandler');

        $attributeGroupForm = $attributeGroupFormBuilder->getForm();
        $attributeGroupForm->handleRequest($request);

        try {
            $handlerResult = $attributeFormHandler->handle($attributeGroupForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_attribute_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/AttributeGroup/create.html.twig',
            [
                'layoutTitle' => $this->trans('New attribute', 'Admin.Navigation.Menu'),
                'attributeGroupForm' => $attributeGroupForm->createView(),
            ]
        );
    }

    /**
     * @param int $attributeGroupId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.')]
    public function editAction(Request $request, int $attributeGroupId): Response
    {
        $attributeGroupFormBuilder = $this->get('PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\AttributeGroupFormBuilder');
        $attributeFormHandler = $this->get('PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\AttributeGroupFormHandler');

        $attributeGroupForm = $attributeGroupFormBuilder->getFormFor($attributeGroupId);

        $attributeGroupForm->handleRequest($request);

        try {
            $handlerResult = $attributeFormHandler->handleFor($attributeGroupId, $attributeGroupForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_attribute_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $formData = $attributeGroupForm->getData();
        $attributeGroupName = $formData['name'][$this->getContextLangId()] ?? reset($formData['name']);

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/AttributeGroup/edit.html.twig',
            [
                'layoutTitle' => $this->trans(
                    'Editing attribute %name%',
                    'Admin.Navigation.Menu',
                    ['%name%' => $attributeGroupName]
                ),
                'attributeGroupForm' => $attributeGroupForm->createView(),
                'attributeGroupId' => $attributeGroupId,
            ]
        );
    }

    /**
     * @param AttributeGroupFilters $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'You do not have permission to export this.')]
    public function exportAction(AttributeGroupFilters $filters): CsvResponse
    {
        $filters = new AttributeGroupFilters(['limit' => null] + $filters->all());
        $attributeGroupGridFactory = $this->get('prestashop.core.grid.factory.attribute_group');
        $attributeGroupGrid = $attributeGroupGridFactory->getGrid($filters);

        $headers = [
            'id_attribute_group' => $this->trans('ID', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'position' => $this->trans('Position', 'Admin.Global'),
        ];

        $data = [];

        foreach ($attributeGroupGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_attribute_group' => $record['id_attribute_group'],
                'name' => $record['name'],
                'position' => $record['position'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('attribute_group_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Updates attribute groups positioning order
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_attribute_groups_index')]
    public function updatePositionAction(Request $request)
    {
        $positionsData = [
            'positions' => $request->request->all('positions'),
        ];

        $positionDefinition = $this->get('prestashop.core.grid.attribute_group.position_definition');
        $positionUpdateFactory = $this->get(PositionUpdateFactoryInterface::class);

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get(GridPositionUpdaterInterface::class);
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
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
     * @param int $attributeGroupId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_attribute_groups_index')]
    public function deleteAction($attributeGroupId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAttributeGroupCommand((int) $attributeGroupId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attribute_groups_index');
    }

    /**
     * Deletes multiple attribute groups by provided ids from request
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_attribute_groups_index')]
    public function bulkDeleteAction(Request $request)
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteAttributeGroupCommand(
                    $this->getAttributeGroupIdsFromRequest($request))
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
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
        $attributeGroupIds = $request->request->all('attribute_group_bulk');

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
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            AttributeGroupConstraintException::class => [
                AttributeGroupConstraintException::EMPTY_NAME => $this->trans(
                    'The field %field_name% is required at least in your default language.',
                    'Admin.Notifications.Error',
                    ['%field_name%' => $this->trans('Name', 'Admin.Global')]
                ),
                AttributeGroupConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],

            CannotAddAttributeGroupException::class => $this->trans(
                'An error occurred while creating the attribute.',
                'Admin.Catalog.Notification'
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
