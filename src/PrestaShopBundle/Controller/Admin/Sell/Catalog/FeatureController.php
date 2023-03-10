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
use Feature;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureFilters;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerListTrait;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerTrait;
use PrestaShopBundle\Bridge\Helper\Listing\HelperBridge\FeatureHelperListBridge;
use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;
use PrestaShopBundle\Bridge\Smarty\FrameworkControllerSmartyTrait;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController implements FrameworkBridgeControllerInterface
{
    use FrameworkBridgeControllerTrait;
    use FrameworkBridgeControllerListTrait;
    use FrameworkControllerSmartyTrait;

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
            ],
        ]);
    }

    /**
     * @deprecated do not use this action, it is not maintained and will eventually be removed
     *
     * This action is only left for a reference of how horizontal migration approach worked.
     * Horizontal migration was cancelled as ineffective, but some parts of it can still be reused.
     * So this should be cleaned up when it is decided which parts can be left and which can still be useful.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function horizontalIndexAction(Request $request): Response
    {
        $this->setHeaderToolbarActions();

        $helperListConfiguration = $this->buildListConfiguration(
            'id_feature',
            // @todo: position update is still handled by legacy ajax controller action. Need to handle in dedicated PR
            'position',
            $request->attributes->get('_route'),
            'id_feature'
        );

        $this->setListFields($helperListConfiguration);
        $this->setListActions($helperListConfiguration);
        $this->processFilters($request, $helperListConfiguration);

        return $this->renderSmarty($this->getHelperListBridge()->generateList($helperListConfiguration));
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

                return $this->redirectToRoute('admin_features_edit', [
                    'featureId' => $featureId,
                ]);
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
     * Render feature edit form
     *
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
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            FeatureConstraintException::class => [
                FeatureConstraintException::EMPTY_NAME => $this->trans(
                    'The field %field_name% is required at least in your default language.',
                    'Admin.Notifications.Error',
                    ['%field_name%' => $this->trans('Name', 'Admin.Global')]
                ),
                FeatureConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                ),
            ],
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

    /**
     * @return ControllerConfiguration
     */
    public function getControllerConfiguration(): ControllerConfiguration
    {
        return $this->buildControllerConfiguration(
            'feature',
            Feature::class,
            'AdminFeatures'
        );
    }

    /**
     * @return FeatureHelperListBridge
     */
    private function getHelperListBridge(): FeatureHelperListBridge
    {
        return $this->get('prestashop.bridge.helper.listing.helper_bridge.feature_helper_list_bridge');
    }

    /**
     * @return void
     */
    private function setHeaderToolbarActions(): void
    {
        $controllerConfiguration = $this->getControllerConfiguration();
        $index = $controllerConfiguration->legacyCurrentIndex;
        $token = $controllerConfiguration->token;

        $controllerConfiguration
            ->addHeaderToolbarAction('new_feature', [
                'href' => $this->generateUrl('admin_features_add'),
                'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new',
            ])
            ->addHeaderToolbarAction('new_feature_value', [
                'href' => $index . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $token,
                'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
                'icon' => 'process-icon-new',
            ])
        ;
    }

    /**
     * Build actions for list.
     *
     * @return void
     */
    private function setListActions(HelperListConfiguration $helperListConfiguration): void
    {
        $helperListConfiguration
            ->addRowAction('view')
            ->addRowAction('edit')
            ->addRowAction('delete')
            ->addToolbarAction('new', [
                'href' => $this->generateUrl('admin_features_add'),
                'desc' => $this->trans('Add new', 'Admin.Actions'),
            ])
            ->addBulkAction('delete', [
                'text' => $this->trans('Delete selected', 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
            ])
        ;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     */
    private function setListFields(HelperListConfiguration $helperListConfiguration): void
    {
        $helperListConfiguration->setFieldsList([
            'id_feature' => [
                'title' => $this->trans('ID', 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', 'Admin.Global'),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', 'Admin.Global'),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', 'Admin.Global'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ]);
    }
}
