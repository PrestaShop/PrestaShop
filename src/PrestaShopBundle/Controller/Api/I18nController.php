<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
