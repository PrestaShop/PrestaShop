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
use PrestaShop\PrestaShop\Adapter\AttributeGroup\AttributeGroupViewDataProvider;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\BulkDeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\DeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\DeleteAttributeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\TranslatableCoreException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Search\Filters\AttributeFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Attributes & Features > Attributes > Attribute
 */
class AttributeController extends PrestaShopAdminController
{
    /**
     * Button name which when submitted indicates that after form submission
     * user wants to be redirected to ADD NEW form to add additional value
     */
    private const SAVE_AND_ADD_BUTTON_NAME = 'save-and-add-new';

    /**
     * Displays Attribute groups > attributes page
     *
     * @param Request $request
     * @param int|string $attributeGroupId
     * @param AttributeFilters $attributeFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_attributes_index', redirectQueryParamsToKeep: ['attributeGroupId'])]
    public function indexAction(
        Request $request, $attributeGroupId,
        AttributeFilters $attributeFilters,
        #[Autowire(service: 'prestashop.core.grid.factory.attribute')]
        GridFactoryInterface $attributeGridFactory,
        AttributeGroupViewDataProvider $attributeGroupViewDataProvider
    ): Response {
        try {
            $attributeGrid = $attributeGridFactory->getGrid($attributeFilters);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_attribute_groups_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attribute/index.html.twig', [
            'attributeGrid' => $this->presentGrid($attributeGrid),
            'attributeGroupId' => $attributeGroupId,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Attribute %name%',
                ['%name%' => $attributeGroupViewDataProvider->getAttributeGroupNameById((int) $attributeGroupId)],
                'Admin.Navigation.Menu'
            ),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Updates attributes positioning order
     *
     * @param Request $request
     * @param int $attributeGroupId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_attributes_index', redirectQueryParamsToKeep: ['attributeGroupId'])]
    public function updatePositionAction(
        Request $request,
        int $attributeGroupId,
        #[Autowire(service: 'prestashop.core.grid.attribute.position_definition')]
        PositionDefinition $positionDefinition,
    ): RedirectResponse {
        $positionsData = [
            'positions' => $request->request->all('positions'),
            'parentId' => $attributeGroupId,
        ];

        try {
            $this->updateGridPosition($positionDefinition, $positionsData);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (TranslatableCoreException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        } catch (Exception $e) {
            $this->addFlashErrors([$e->getMessage()]);
        }

        return $this->redirectToRoute('admin_attributes_index', [
            'attributeGroupId' => $attributeGroupId,
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to create this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.attribute_form_builder')]
        FormBuilderInterface $attributeFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.attribute_form_handler')]
        FormHandlerInterface $attributeFormHandler
    ): Response {
        $attributeGroupId = (int) $request->query->get('attributeGroupId');

        $attributeForm = $attributeFormBuilder->getForm([], ['attribute_group' => $attributeGroupId]);
        $attributeForm->handleRequest($request);

        try {
            $handlerResult = $attributeFormHandler->handle($attributeForm);
            $attributeFormData = $attributeForm->getData();

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                // Save and create a new attribute value for the same attribute group
                if ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME)) {
                    return $this->redirectToRoute('admin_attributes_create', ['attributeGroupId' => $attributeGroupId]);
                }

                return $this->redirectToRoute('admin_attributes_index', ['attributeGroupId' => $attributeFormData['attribute_group']]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Attribute/create.html.twig',
            [
                'layoutTitle' => $this->trans('New attribute value', [], 'Admin.Navigation.Menu'),
                'attributeForm' => $attributeForm->createView(),
                'attributeGroupId' => $attributeGroupId,
            ]
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.')]
    public function editAction(
        Request $request,
        int $attributeId,
        int $attributeGroupId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.attribute_form_builder')]
        FormBuilderInterface $attributeFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.attribute_form_handler')]
        FormHandlerInterface $attributeFormHandler,
    ): Response {
        $attributeForm = $attributeFormBuilder->getFormFor($attributeId, [], ['attribute_group' => $attributeGroupId])
            ->handleRequest($request);

        $formData = $attributeForm->getData();
        $attributeName = $formData['name'][$this->getLanguageContext()->getId()] ?? reset($formData['name']);

        try {
            $handlerResult = $attributeFormHandler->handleFor($attributeId, $attributeForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                // Save and create a new attribute value for the same attribute group
                if ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME)) {
                    return $this->redirectToRoute('admin_attributes_create', ['attributeGroupId' => $attributeGroupId]);
                }

                return $this->redirectToRoute('admin_attributes_index', ['attributeGroupId' => $attributeGroupId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Sell/Catalog/Attribute/edit.html.twig',
            [
                'layoutTitle' => $this->trans(
                    'Editing attribute value %name%',
                    ['%name%' => $attributeName],
                    'Admin.Navigation.Menu'
                ),
                'attributeForm' => $attributeForm->createView(),
                'attributeGroupId' => $attributeGroupId,
            ]
        );
    }

    /**
     * Deletes attribute
     *
     * @param int $attributeGroupId
     * @param int $attributeId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_attributes_index', redirectQueryParamsToKeep: ['attributeGroupId'])]
    public function deleteAction(int $attributeGroupId, int $attributeId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteAttributeCommand((int) $attributeId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
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
     * @param int $attributeGroupId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_attributes_index', redirectQueryParamsToKeep: ['attributeGroupId'])]
    public function bulkDeleteAction(int $attributeGroupId, Request $request)
    {
        try {
            $this->dispatchCommand(new BulkDeleteAttributeCommand(
                $this->getAttributeIdsFromRequest($request))
            );
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
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
        $attributeIds = $request->request->all('attribute_bulk');

        foreach ($attributeIds as $i => $attributeId) {
            $attributeIds[$i] = (int) $attributeId;
        }

        return $attributeIds;
    }

    /**
     * @param AttributeFilters $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'You do not have permission to export this.')]
    public function exportAction(
        AttributeFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.attribute')]
        GridFactoryInterface $attributeGridFactory
    ): CsvResponse {
        $filters = new AttributeFilters(['limit' => null] + $filters->all());
        $attributeGrid = $attributeGridFactory->getGrid($filters);
        $attributeRecords = $attributeGrid->getData()->getRecords()->all();

        $data = [];
        $hasColor = false;

        foreach ($attributeRecords as $record) {
            $dataToPush = [];
            $dataToPush['id_attribute'] = $record['id_attribute'];
            $dataToPush['id_attribute_group'] = $record['id_attribute_group'];
            $dataToPush['name'] = $record['name'];
            if (!empty($record['color'])) {
                $dataToPush['color'] = $record['color'];
                $hasColor = true;
            }
            $dataToPush['position'] = $record['position'];
            $data[] = $dataToPush;
        }

        $headers = [];
        $headers['id_attribute'] = $this->trans('ID', [], 'Admin.Global');
        $headers['id_attribute_group'] = $this->trans('Attribute Group ID', [], 'Admin.Global');
        $headers['name'] = $this->trans('Name', [], 'Admin.Global');
        if ($hasColor) {
            $headers['color'] = $this->trans('Color', [], 'Admin.Global');
        }
        $headers['id_attribute'] = $this->trans('ID', [], 'Admin.Global');
        $headers['position'] = $this->trans('Position', [], 'Admin.Global');

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('attribute_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        $notFoundMessage = $this->trans(
            'The object cannot be loaded (or found).',
            [],
            'Admin.Notifications.Error'
        );

        return [
            AttributeNotFoundException::class => $notFoundMessage,
            AttributeGroupNotFoundException::class => $notFoundMessage,
            AttributeConstraintException::class => [
                AttributeConstraintException::INVALID_NAME => $this->trans(
                    'Attribute name is invalid',
                    [],
                    'Admin.Notifications.Error'
                ),
                AttributeConstraintException::INVALID_COLOR => $this->trans(
                    'Attribute color is invalid ',
                    [],
                    'Admin.Notifications.Error'
                ),
                AttributeConstraintException::INVALID_ATTRIBUTE_GROUP_ID => $this->trans(
                    'Attribute group is invalid',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            DeleteAttributeException::class => [
                DeleteAttributeException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                DeleteAttributeException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
