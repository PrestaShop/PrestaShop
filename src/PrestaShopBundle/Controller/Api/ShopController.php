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

namespace PrestaShopBundle\Controller\Api;

use PrestaShop\PrestaShop\Core\CommandBus\TacticianCommandBusAdapter;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

class ShopController extends ApiController
{
    /**
     * @var TacticianCommandBusAdapter
     */
    public $queryBus;

    /**
     * @var Serializer
     */
    public $serializer;

    /**
     * @param string $searchTerm
     *
     * @return JsonResponse
     */
    public function listShopsAction(string $searchTerm): JsonResponse
    {
        try {
            $result = [];
            $result['data'] = $this->queryBus->handle(new SearchShops((string) $searchTerm));
            $statusCode = empty($result['data']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            $json = $this->serializer->serialize($result, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ], []));
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessage($e)],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse($json, $statusCode, [], true);
    }
}
