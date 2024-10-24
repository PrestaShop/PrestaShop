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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\FeatureValuesChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotDeleteFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Factory\FeatureValueGridFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureValueController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    /**
     * Button name which when submitted indicates that after form submission
     * user wants to be redirected to ADD NEW form to add additional value
     */
    private const SAVE_AND_ADD_BUTTON_NAME = 'save-and-add-new';

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        int $featureId,
        Request $request,
        FeatureValueFilters $filters,
        #[Autowire(service: FeatureValueGridFactory::class)]
        GridFactoryInterface $featureValueGridFactory,
    ): Response {
        try {
            /** @var EditableFeature $editableFeature */
            $editableFeature = $this->dispatchQuery(new GetFeatureForEditing($featureId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_features_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'featureValueGrid' => $this->presentGrid($featureValueGridFactory->getGrid($filters)),
            'layoutTitle' => $editableFeature->getName()[$this->getLanguageContext()->getId()],
            'layoutHeaderToolbarBtn' => [
                'add_feature_value' => [
                    'href' => $this->generateUrl('admin_feature_values_add', ['featureId' => $filters->getFeatureId()]),
                    'desc' => $this->trans('Add new feature value', [], 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.feature_value_form_builder')]
        FormBuilderInterface $featureValueFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.feature_value_form_handler')]
        FormHandlerInterface $featureValueFormHandler
    ): Response {
        $featureId = $request->query->getInt('featureId');

        try {
            $featureValueForm = $featureValueFormBuilder->getForm(['feature_id' => $featureId]);
            $featureValueForm->handleRequest($request);
            $handlerResult = $featureValueFormHandler->handle($featureValueForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                // Case 1 - save and stay, user entered the form from feature value list
                if ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME) && $featureId) {
                    return $this->redirectToRoute('admin_feature_values_add', ['featureId' => $featureId]);
                // Case 2 - save and stay, user entered the form from feature list
                } elseif ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME)) {
                    return $this->redirectToRoute('admin_feature_values_add');
                // Case 3 - save and exit, user entered the form from feature value list
                } elseif ($featureId) {
                    return $this->redirectToRoute('admin_feature_values_index', ['featureId' => $featureId]);
                }

                // Case 4 - save and exit, if user entered the form from feature list
                return $this->redirectToRoute('admin_features_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_features_index');
        }

        // Resolve a link to use when cancelling the form
        if ($featureId) {
            $cancelLink = $this->generateUrl('admin_feature_values_index', ['featureId' => $featureId]);
        } else {
            $cancelLink = $this->generateUrl('admin_features_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/create.html.twig', [
            'featureId' => $featureId,
            'featureValueForm' => $featureValueForm->createView(),
            'layoutTitle' => $this->trans('New Feature Value', [], 'Admin.Navigation.Menu'),
            'cancelLink' => $cancelLink,
        ]);
    }

    /**
     * @param int $featureId
     * @param int $featureValueId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(
        int $featureId,
        int $featureValueId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.feature_value_form_builder')]
        FormBuilderInterface $featureValueFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.feature_value_form_handler')]
        FormHandlerInterface $featureValueFormHandler
    ): Response {
        try {
            $featureValueForm = $featureValueFormBuilder->getFormFor($featureValueId);
            $featureValueForm->handleRequest($request);
            $handlerResult = $featureValueFormHandler->handleFor((int) $featureValueId, $featureValueForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                if ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME)) {
                    return $this->redirectToRoute('admin_feature_values_add', [
                        'featureId' => $featureId,
                    ]);
                }

                return $this->redirectToRoute('admin_feature_values_index', [
                    'featureId' => $featureId,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_features_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/edit.html.twig', [
            'featureId' => $featureId,
            'featureValueForm' => $featureValueForm->createView(),
            'layoutTitle' => $this->trans(
                'Feature value',
                [],
                'Admin.Navigation.Menu',
            ),
            'cancelLink' => $this->generateUrl('admin_feature_values_index', ['featureId' => $featureId]),
        ]);
    }

    /**
     * @param FeatureValueFilters $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportAction(
        FeatureValueFilters $filters,
        #[Autowire(service: FeatureValueGridFactory::class)]
        GridFactoryInterface $gridFactory
    ): CsvResponse {
        $filtersParameters = $filters->all();
        $filtersParameters['filters']['feature_id'] = $filters->getFeatureId();
        $filtersParameters['filters']['language_id'] = $filters->getLanguageId();
        $filters = new FeatureValueFilters(['limit' => null] + $filtersParameters);
        $filters->setNeedsToBePersisted(false);

        $headers = [
            'id_feature_value' => $this->trans('ID', [], 'Admin.Global'),
            'value' => $this->trans('Value', [], 'Admin.Global'),
        ];

        $data = [];

        foreach ($gridFactory->getGrid($filters)->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_feature_value' => $record['id_feature_value'],
                'value' => $record['value'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('feature_' . $filters->getFeatureId() . '_values_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param int $featureId
     * @param int $featureValueId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteAction(int $featureId, int $featureValueId): Response
    {
        try {
            $this->dispatchCommand(new DeleteFeatureValueCommand($featureValueId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_feature_values_index', [
            'featureId' => $featureId,
        ]);
    }

    /**
     * @param int $featureId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteAction(int $featureId, Request $request): Response
    {
        try {
            $this->dispatchCommand(new BulkDeleteFeatureValueCommand($this->getBulkActionIds($request, 'feature_value_bulk')));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_feature_values_index', [
            'featureId' => $featureId,
        ]);
    }

    /**
     * Get all values for a given feature.
     *
     * @param int $featureId The feature Id
     *
     * @return JsonResponse features list
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('read', 'AdminProducts')")]
    public function getFeatureValuesAction(
        int $featureId,
        FeatureValuesChoiceProvider $featureValuesChoiceProvider
    ): JsonResponse {
        if ($featureId == 0) {
            return new JsonResponse();
        }

        $featuresChoices = $featureValuesChoiceProvider->getChoices(['feature_id' => $featureId, 'custom' => false]);

        $data = [];
        if (count($featuresChoices) !== 0) {
            $data[] = [
                'id' => 0,
                'value' => $this->trans('Choose a value', [], 'Admin.Catalog.Feature'),
            ];
        }

        foreach ($featuresChoices as $featureName => $featureId) {
            $data[] = [
                'id' => $featureId,
                'value' => $featureName,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Changes feature value position
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function updatePositionAction(
        int $featureId,
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.feature_value.position_definition')]
        PositionDefinition $positionDefinition,
    ): Response {
        try {
            $this->updateGridPosition($positionDefinition, [
                'positions' => $request->request->all('positions'),
                'parentId' => $featureId,
            ]);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('admin_feature_values_index', [
            'featureId' => $featureId,
        ]);
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function getErrorMessages(): array
    {
        return [
            FeatureNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            FeatureValueNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            BulkFeatureValueException::class => [
                BulkFeatureValueException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteFeatureValueException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
        ];
    }
}
