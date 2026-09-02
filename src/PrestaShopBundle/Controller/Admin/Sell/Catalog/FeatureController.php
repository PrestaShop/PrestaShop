<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureFeature;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotDeleteFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    public function __construct(
        private readonly FeatureFeature $featureFeature
    ) {
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        FeatureFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.feature')]
        GridFactoryInterface $featureGridFactory,
    ): Response {
        $showcaseCardIsClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed(
                $this->getEmployeeContext()->getEmployee()->getId(),
                ShowcaseCard::FEATURES_CARD
            )
        );

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'featureGrid' => $this->presentGrid($featureGridFactory->getGrid($filters)),
            'settingsTipMessage' => $this->getSettingsTipMessage(),
            'showcaseCardName' => ShowcaseCard::FEATURES_CARD,
            'isShowcaseCardClosed' => $showcaseCardIsClosed,
            'layoutHeaderToolbarBtn' => [
                'add_feature' => [
                    'href' => $this->generateUrl('admin_features_add'),
                    'desc' => $this->trans('Add new feature', [], 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
                'add_feature_value' => [
                    'href' => $this->generateUrl('admin_feature_values_add'),
                    'desc' => $this->trans('Add new feature value', [], 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * Create feature action.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.feature_form_builder')]
        FormBuilderInterface $featureFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.feature_form_handler')]
        FormHandlerInterface $featureFormHandler
    ): Response {
        if (!$this->isFeatureEnabled()) {
            return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/create.html.twig', [
                'showDisabledFeatureWarning' => true,
                'layoutTitle' => $this->trans('New feature', [], 'Admin.Navigation.Menu'),
            ]);
        }

        $featureForm = $featureFormBuilder->getForm();
        $featureForm->handleRequest($request);

        try {
            $handlerResult = $featureFormHandler->handle($featureForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_features_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/create.html.twig', [
            'featureForm' => $featureForm->createView(),
            'layoutTitle' => $this->trans('New feature', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Edit feature action.
     *
     * @param int $featureId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function editAction(
        int $featureId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.feature_form_builder')]
        FormBuilderInterface $featureFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.feature_form_handler')]
        FormHandlerInterface $featureFormHandler
    ): Response {
        try {
            $editableFeature = $this->dispatchQuery(new GetFeatureForEditing($featureId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_features_index');
        }

        if (!$this->isFeatureEnabled()) {
            return $this->renderEditForm([
                'showDisabledFeatureWarning' => true,
                'editableFeature' => $editableFeature,
            ]);
        }

        $featureForm = $featureFormBuilder->getFormFor($featureId);
        $featureForm->handleRequest($request);

        try {
            $handlerResult = $featureFormHandler->handleFor((int) $featureId, $featureForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_features_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->renderEditForm([
            'featureForm' => $featureForm->createView(),
            'editableFeature' => $editableFeature,
        ]);
    }

    /**
     * @param FeatureFilters $filters
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportAction(
        FeatureFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.grid_factory.feature')]
        GridFactoryInterface $featuresGridFactory
    ): CsvResponse {
        $filters = new FeatureFilters($filters->getShopConstraint(), ['limit' => null] + $filters->all());

        $featuresGrid = $featuresGridFactory->getGrid($filters);

        $headers = [
            'id_feature' => $this->trans('ID', [], 'Admin.Global'),
            'name' => $this->trans('Name', [], 'Admin.Global'),
            'values_count' => $this->trans('values', [], 'Admin.Global'),
            'position' => $this->trans('position', [], 'Admin.Global'),
        ];

        $data = [];

        foreach ($featuresGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_feature' => $record['id_feature'],
                'name' => $record['name'],
                'values_count' => $record['values_count'],
                'position' => $record['position'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('features_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * @param int $featureId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function deleteAction(int $featureId): Response
    {
        try {
            $this->dispatchCommand(new DeleteFeatureCommand($featureId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteAction(Request $request): Response
    {
        try {
            $this->dispatchCommand(new BulkDeleteFeatureCommand($this->getBulkActionIds($request, 'feature_bulk')));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return Response
     */
    private function renderEditForm(array $parameters = []): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/edit.html.twig', $parameters + [
            'contextLangId' => $this->getConfiguration()->get('PS_LANG_DEFAULT'),
            'layoutTitle' => $this->trans(
                'Editing feature %name%',
                [
                    '%name%' => $parameters['editableFeature']->getName()[$this->getConfiguration()->get('PS_LANG_DEFAULT')],
                ],
                'Admin.Navigation.Menu'
            ),
        ]);
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function getAllFeatureGroupsAction(FeatureRepository $featureRepository): JsonResponse
    {
        $features = $featureRepository->getFeaturesWithValues($this->getLanguageContext()->getId(), $this->getShopContext()->getAssociatedShopIds());

        return $this->json($this->formatFeatureGroupsForPresentation($features));
    }

    /**
     * @param array<int, array{feature_id: int, name: string, values: array<int, array{item_id: int, name: string}>}> $features
     *
     * @return array<int, array{id: int, name: string, feature_values: array<int, array{id: int, name: string}>}>
     */
    private function formatFeatureGroupsForPresentation(array $features): array
    {
        $formattedGroups = [];
        foreach ($features as $feature) {
            $featureValues = [];

            foreach ($feature['values'] as $featureValue) {
                $featureValues[] = [
                    'id' => $featureValue['item_id'],
                    'name' => $featureValue['name'],
                ];
            }

            $formattedGroups[] = [
                'id' => $feature['feature_id'],
                'name' => $feature['name'],
                'feature_values' => $featureValues,
            ];
        }

        return $formattedGroups;
    }

    /**
     * Get translated error messages for feature exceptions
     *
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
            FeatureConstraintException::class => [
                FeatureConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Name', [], 'Admin.Global'))],
                    'Admin.Notifications.Error'
                ),
            ],
            BulkFeatureException::class => [
                BulkFeatureException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteFeatureException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * Check if Features functionality is enabled in the shop.
     *
     * @return bool
     */
    private function isFeatureEnabled(): bool
    {
        return $this->featureFeature->isActive();
    }

    /**
     * @return string|null
     */
    private function getSettingsTipMessage()
    {
        if ($this->isFeatureEnabled()) {
            return null;
        }

        $urlOpening = sprintf('<a href="%s">', $this->generateUrl('admin_performance'));
        $urlEnding = '</a>';

        return $this->trans(
            'The features are disabled on your store. Go to %sAdvanced Parameters > Performance%s to edit settings.',
            [$urlOpening, $urlEnding],
            'Admin.Catalog.Notification'
        );
    }
}
