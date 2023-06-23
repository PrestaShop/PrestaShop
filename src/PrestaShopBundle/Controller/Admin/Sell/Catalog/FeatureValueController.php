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
use PrestaShop\PrestaShop\Core\Grid\Factory\FeatureValueGridFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureValueController extends FrameworkBundleAdminController
{
    /**
     * Button name which when submitted indicates that after form submission
     * user wants to be redirected to ADD NEW form to add additional value
     */
    private const SAVE_AND_ADD_BUTTON_NAME = 'save-and-add-new';

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request, FeatureValueFilters $filters): Response
    {
        $featureValueGridFactory = $this->get(FeatureValueGridFactory::class);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'featureValueGrid' => $this->presentGrid($featureValueGridFactory->getGrid($filters)),
            'layoutHeaderToolbarBtn' => [
                'add_feature_value' => [
                    'href' => $this->generateUrl('admin_feature_values_add', ['featureId' => $filters->getFeatureId()]),
                    'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param int $featureId
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(int $featureId, Request $request): Response
    {
        $featureValueFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.feature_value_form_builder');
        $featureValueFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.feature_value_form_handler');

        $featureValueForm = $featureValueFormBuilder->getForm(['feature_id' => $featureId]);
        $featureValueForm->handleRequest($request);

        try {
            $handlerResult = $featureValueFormHandler->handle($featureValueForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                if ($request->request->has(self::SAVE_AND_ADD_BUTTON_NAME)) {
                    return $this->redirectToRoute('admin_feature_values_add', [
                        'featureId' => $featureId,
                    ]);
                }

                return $this->redirectToRoute('admin_features_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/create.html.twig', [
            'featureId' => $featureId,
            'featureValueForm' => $featureValueForm->createView(),
            'layoutTitle' => $this->trans('New Feature Value', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $featureValueId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $featureId, int $featureValueId, Request $request): Response
    {
        $featureValueFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.feature_value_form_builder');
        $featureValueFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.feature_value_form_handler');

        $featureValueForm = $featureValueFormBuilder->getFormFor($featureValueId);
        $featureValueForm->handleRequest($request);

        try {
            $handlerResult = $featureValueFormHandler->handleFor((int) $featureValueId, $featureValueForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

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
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValue/edit.html.twig', [
            'featureId' => $featureId,
            'featureValueForm' => $featureValueForm->createView(),
            'layoutTitle' => $this->trans(
                'Feature value',
                'Admin.Navigation.Menu',
            ),
        ]);
    }

    private function getErrorMessages(): array
    {
        //@todo:
        return [];
    }
}
