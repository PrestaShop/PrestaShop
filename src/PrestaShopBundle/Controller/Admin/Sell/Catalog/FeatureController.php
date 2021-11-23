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
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController
{
    /**
     * Create feature action.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
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
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                //@todo change route to index when it's migrated
                return $this->redirectToRoute('admin_features_create');
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
    public function editAction($featureId, Request $request)
    {
        try {
            $editableFeature = $this->getQueryBus()->handle(new GetFeatureForEditing((int) $featureId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            // @todo change route to features index when it's migrated
            return $this->redirectToRoute('admin_features_create');
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
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

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
