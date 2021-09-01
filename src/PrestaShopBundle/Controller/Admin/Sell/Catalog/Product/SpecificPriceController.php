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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class SpecificPriceController extends FrameworkBundleAdminController
{
    public function createAction(int $productId, Request $request): JsonResponse
    {
        $form = $this->getFormBuilder()->getForm(['product_id' => $productId]);
        $form->handleRequest($request);
        $formHandler = $this->getFormHandler();

        try {
            $result = $formHandler->handle($form);

            if ($result->getIdentifiableObjectId()) {
                return $this->json(['specificPriceId' => $result->getIdentifiableObjectId()]);
            }
        } catch (Exception $e) {
            return $this->json([
                'errors' => [
                    $this->getFallbackErrorMessage(get_class($e), $e->getCode()),
                ],
            ]);
        }

        //@todo:
        return $this->json('test');
    }

    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.specific_price_form_builder');
    }

    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.specific_price_form_handler');
    }
}
