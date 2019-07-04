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
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Exception\FeatureValueConstraintException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Sell > Catalog > Attributes & Features > Features > Feature Values" pages
 */
class FeatureValueController extends FrameworkBundleAdminController
{
    /**
     * Create feature value action.
     *
     * @param Request $request
     * @param int $featureId
     *
     * @return Response
     */
    public function createAction(Request $request, $featureId)
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.feature_value_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.feature_value_form_handler');
        $form = $formBuilder->getForm([
            'featureId' => $featureId,
        ]);

        $form->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($form);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));
                //@todo change route to index when it's migrated
                return $this->redirectToRoute('admin_feature_values_create', ['featureId' => $featureId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/FeatureValues/create.html.twig', [
            'featureValueForm' => $form->createView(),
        ]);
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            FeatureNotFoundException::class => $this->trans('Invalid feature selected', 'Admin.Catalog.Feature'),
            FeatureValueConstraintException::class => [
                FeatureValueConstraintException::INVALID_VALUE => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Value', 'Admin.Global'))]
                ),
            ],
        ];
    }
}
