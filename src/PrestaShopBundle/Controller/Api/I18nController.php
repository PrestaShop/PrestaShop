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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Api;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class I18nController extends ApiController
{
    /**
     * Show translation for page-app build with vue-js.
     *
     * No access restrictions because it is required for VueJs translations
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listTranslationAction(Request $request)
    {
        try {
            $page = $request->attributes->get('page');

            try {
                $translationClass = $this->container->get('prestashop.translation.api.' . $page);
            } catch (Exception $exception) {
                throw new BadRequestHttpException($exception->getMessage());
            }
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }

        return $this->jsonResponse($translationClass->getFormattedTranslations(), $request);
    }
}
