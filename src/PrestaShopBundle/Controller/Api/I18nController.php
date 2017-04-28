<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Api;

use Exception;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class I18nController extends ApiController
{
    public function listTranslationAction(Request $request)
    {
        try {
            $page = $request->attributes->get('page');

            try {
                $translationClass = $this->container->get('prestashop.translation.api.'.$page);
            }
            catch (Exception $exception) {
                throw new Exception('This \'page\' param is not valid.');
            }
        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }

        return new JsonResponse($translationClass->getTranslations());
    }

    public function listDomainTranslationAction(Request $request)
    {
        try {
            $translationProvider = $this->container->get('prestashop.translation.search_provider');

            $translationProvider->setLocale($request->attributes->get('locale'));
            $translationProvider->setDomain($request->attributes->get('domain'));

            $type = 'get' . ucfirst($request->attributes->get('type'));

            $result = $this->{$type}($translationProvider);

            return new JsonResponse($result);

        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * Get domain from Search provider
     *
     * @param SearchProvider $searchProvider
     * @return mixed
     */
    public function getDomain(SearchProvider $searchProvider)
    {
        return current($searchProvider->getMessageCatalogue()->all());
    }

    /**
     * Get info from from Search provider catalog
     *
     * @param SearchProvider $searchProvider
     * @return mixed
     */
    public function getInfo(SearchProvider $searchProvider)
    {
        $info = array(
            'locale' => $searchProvider->getLocale(),
            'domain' => $searchProvider->getDomain(),
            'missing' => 0,
            'total' => 0,
        );

        $catalog = current($searchProvider->getMessageCatalogue()->all());

        foreach ($catalog as $original => $translated) {
            if ($original === $translated) {
                $info['missing']++;
            }

            $info['total']++;
        }

        return $info;
    }
}
