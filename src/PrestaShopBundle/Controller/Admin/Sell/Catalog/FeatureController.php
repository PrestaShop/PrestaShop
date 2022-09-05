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
use PrestaShopBundle\Bridge\AdminController\Action\HeaderToolbarAction;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerListTrait;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerTrait;
use PrestaShopBundle\Bridge\Helper\Listing\Action\ListBulkAction;
use PrestaShopBundle\Bridge\Helper\Listing\Action\ListHeaderToolbarAction;
use PrestaShopBundle\Bridge\Helper\Listing\Action\ListRowAction;
use PrestaShopBundle\Bridge\Helper\Listing\Field\Field;
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
    public function indexAction(Request $request): Response
    {
        $this->setToolbarActions();

        $helperListConfiguration = $this->buildListConfiguration(
            'id_feature',
            'id_feature',
            'position'
        );

        $this->setListFields($helperListConfiguration);
        $this->setListActions($helperListConfiguration);

        //@todo: seems this actually isn't used. List filters url directs to legacy controller and FiltersHelper never runs
        $filtersHelper = $this->getFiltersProcessor();
        if ($request->request->has('submitResetfeature')) {
            $filtersHelper->resetFilters($helperListConfiguration, $request);
        }

        $filtersHelper->processFilter($request, $helperListConfiguration);

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
    public function editAction($featureId, Request $request): Response
    {
        try {
            $editableFeature = $this->getQueryBus()->handle(new GetFeatureForEditing((int) $featureId));
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
            $handlerResult = $featureFormHandler->handleFor($featureId, $featureForm);

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
    private function setToolbarActions(): void
    {
        $controllerConfiguration = $this->getControllerConfiguration();
        $index = $controllerConfiguration->legacyCurrentIndex;
        $token = $controllerConfiguration->token;

        $controllerConfiguration->addToolbarAction(new HeaderToolbarAction('new_feature', [
            //@todo: replace by $this->generateUrl('admin_features_add') when creation is fully migrated
            'href' => $index . '&addfeature&token=' . $token,
            'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]));
        $controllerConfiguration->addToolbarAction(new HeaderToolbarAction('new_feature_value', [
            'href' => $index . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $token,
            'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
            'icon' => 'process-icon-new',
        ]));
    }

    /**
     * Build actions for list.
     *
     * @return void
     */
    private function setListActions(HelperListConfiguration $helperListConfiguration): void
    {
        $controllerConfiguration = $this->getControllerConfiguration();

        $this->addActionList(new ListHeaderToolbarAction('new', [
            //@todo: replace by $this->generateUrl('admin_features_add') when creation is fully migrated
            //@todo: can i generate link without using controller config?
            'href' => $controllerConfiguration->legacyCurrentIndex . '&addfeature&token=' . $controllerConfiguration->token,
            'desc' => $this->trans('Add new', 'Admin.Actions'),
        ]), $helperListConfiguration);

        $this->addActionList(new ListRowAction('view'), $helperListConfiguration);
        $this->addActionList(new ListRowAction('edit'), $helperListConfiguration);
        $this->addActionList(new ListRowAction('delete'), $helperListConfiguration);

        $this->addActionList(new ListBulkAction('delete', [
            'text' => $this->trans('Delete selected', 'Admin.Actions'),
            'icon' => 'icon-trash',
            'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
        ]), $helperListConfiguration);
    }

    /**
     * Define fields in the list.
     *
     * @return void
     */
    private function setListFields(HelperListConfiguration $helperListConfiguration): void
    {
        $this->addListField(new Field(
            'id_feature', [
                'title' => $this->trans('ID', 'Admin.Global', []),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ]
        ), $helperListConfiguration);
        $this->addListField(new Field(
            'name', [
                'title' => $this->trans('Name', 'Admin.Global', []),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ]
        ), $helperListConfiguration);
        $this->addListField(new Field(
            'value', [
                'title' => $this->trans('Values', 'Admin.Global', []),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ]
        ), $helperListConfiguration);
        $this->addListField(new Field(
            'position', [
                'title' => $this->trans('Position', 'Admin.Global', []),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ]
        ), $helperListConfiguration);
    }

    /**
     * Render feature edit form
     *
     * @param array $parameters
     *
     * @return Response
     */
    private function renderEditForm(array $parameters = [])
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/edit.html.twig', $parameters + [
            'contextLangId' => $this->configuration->get('PS_LANG_DEFAULT'),
        ]);
    }

    /**
     * Get translated error messages for feature exceptions
     *
     * @return array
     */
    private function getErrorMessages()
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
    private function isFeatureEnabled()
    {
        return $this->get('prestashop.adapter.feature.feature')->isActive();
    }
}
