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
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotDeleteFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController
{
    use BulkActionsTrait;

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request, FeatureFilters $filters): Response
    {
        $featureGridFactory = $this->get('prestashop.core.grid.grid_factory.feature');

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed(
                (int) $this->getContext()->employee->id,
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
                    'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
                'add_feature_value' => [
                    'href' => $this->generateUrl('admin_feature_values_add'),
                    'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * Create feature action.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if (!$this->isFeatureEnabled()) {
            return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/create.html.twig', [
                'showDisabledFeatureWarning' => true,
                'layoutTitle' => $this->trans('New feature', 'Admin.Navigation.Menu'),
            ]);
        }

        $featureFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.feature_form_builder');
        $featureFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.feature_form_handler');

        $featureForm = $featureFormBuilder->getForm();
        $featureForm->handleRequest($request);

        try {
            $handlerResult = $featureFormHandler->handle($featureForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_features_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/create.html.twig', [
            'featureForm' => $featureForm->createView(),
            'layoutTitle' => $this->trans('New feature', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Edit feature action.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $featureId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $featureId, Request $request): Response
    {
        try {
            $editableFeature = $this->getQueryBus()->handle(new GetFeatureForEditing($featureId));
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

        $featureFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.feature_form_builder');
        $featureFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.feature_form_handler');

        $featureForm = $featureFormBuilder->getFormFor($featureId);
        $featureForm->handleRequest($request);

        try {
            $handlerResult = $featureFormHandler->handleFor((int) $featureId, $featureForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

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
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param FeatureFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(FeatureFilters $filters): CsvResponse
    {
        $filters = new FeatureFilters($filters->getShopConstraint(), ['limit' => null] + $filters->all());

        $featuresGridFactory = $this->get('prestashop.core.grid.grid_factory.feature');
        $featuresGrid = $featuresGridFactory->getGrid($filters);

        $headers = [
            'id_feature' => $this->trans('ID', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'values_count' => $this->trans('values', 'Admin.Global'),
            'position' => $this->trans('position', 'Admin.Global'),
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
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $featureId
     *
     * @return Response
     */
    public function deleteAction(int $featureId): Response
    {
        try {
            $this->getCommandBus()->handle(new DeleteFeatureCommand($featureId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteFeatureCommand($this->getBulkActionIds($request, 'feature_bulk')));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @param array $parameters
     *
     * @return Response
     */
    private function renderEditForm(array $parameters = []): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/edit.html.twig', $parameters + [
            'contextLangId' => $this->getConfiguration()->get('PS_LANG_DEFAULT'),
            'layoutTitle' => $this->trans(
                'Editing feature %name%',
                'Admin.Navigation.Menu',
                [
                    '%name%' => $parameters['editableFeature']->getName()[$this->getConfiguration()->get('PS_LANG_DEFAULT')],
                ]
            ),
        ]);
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
                'Admin.Notifications.Error'
            ),
            FeatureConstraintException::class => [
                FeatureConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],
            BulkFeatureException::class => [
                BulkFeatureException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteFeatureException::class => $this->trans(
                'An error occurred while deleting the object.',
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
        return $this->get('prestashop.adapter.feature.feature')->isActive();
    }

    /**
     * @return string
     */
    private function getSettingsTipMessage(): string
    {
        $urlOpening = sprintf('<a href="%s">', $this->get('router')->generate('admin_performance'));
        $urlEnding = '</a>';

        if ($this->isFeatureEnabled()) {
            return $this->trans(
                'The features are enabled on your store. Go to %sAdvanced Parameters > Performance%s to edit settings.',
                'Admin.Catalog.Notification',
                [$urlOpening, $urlEnding]
            );
        }

        return $this->trans(
            'The features are disabled on your store. Go to %sAdvanced Parameters > Performance%s to edit settings.',
            'Admin.Catalog.Notification',
            [$urlOpening, $urlEnding]
        );
    }
}
