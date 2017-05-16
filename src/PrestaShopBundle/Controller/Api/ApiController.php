<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Api\QueryParamsCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param HttpException $exception
     * @return JsonResponse
     */
    protected function handleException(HttpException $exception)
    {
        $this->logger->info($exception->getMessage());

        return new JsonResponse(array('error' => $exception->getMessage()), $exception->getStatusCode());
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function guardAgainstInvalidJsonBody($content)
    {
        $decodedContent = json_decode($content, true);

        $jsonLastError = json_last_error();
        if ($jsonLastError !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('The request body should be a valid JSON content');
        }

        return $decodedContent;
    }

    /**
     * @see \Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand
     */
    protected function clearCache()
    {
        $cacheRefresh = $this->container->get('prestashop.cache.refresh');

        try {
            $cacheRefresh->addCacheClear();
            $cacheRefresh->execute();
        } catch (\Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }
    }

    /**
     * Add additional info to JSON return
     *
     * @param Request $request
     * @param QueryParamsCollection|null $queryParams
     * @param array $headers
     * @return array
     */
    protected function addAdditionalInfo(Request $request, QueryParamsCollection $queryParams = null, $headers = array())
    {
        $router = $this->container->get('router');

        $queryParamsArray = array();
        if (!is_null($queryParams)) {
            $queryParamsArray = $queryParams->getQueryParams();
        }

        $allParams = $allParamsWithoutPagination = array_merge($request->attributes->get('_route_params'), $queryParamsArray, $request->query->all());
        unset($allParamsWithoutPagination['page_index'], $allParamsWithoutPagination['page_size']);

        $info = array(
            'current_url' => $router->generate($request->attributes->get('_route'), $allParams),
            'current_url_without_pagination' => $router->generate($request->attributes->get('_route'), $allParamsWithoutPagination)
        );

        if (array_key_exists('page_index', $allParams) && $allParams['page_index'] > 1) {
            $previousParams = $allParams;
            if (array_key_exists('page_index', $previousParams)) {
                $previousParams['page_index']--;
            }
            $info['previous_url'] = $router->generate($request->attributes->get('_route'), $previousParams);
        }

        if (array_key_exists('Total-Pages', $headers) &&
            array_key_exists('page_index', $allParams) &&
            $headers['Total-Pages'] > $allParams['page_index']) {
            $nextParams = $allParams;
            if (array_key_exists('page_index', $nextParams)) {
                $nextParams['page_index']++;
            }
            $info['next_url'] = $router->generate($request->attributes->get('_route'), $nextParams);
        }


        if(array_key_exists('Total-Pages', $headers)) {
            $info['total_page'] = $headers['Total-Pages'];
        }

        if (!is_null($queryParams)) {
            $info['page_index'] = $queryParamsArray['page_index'];
            $info['page_size'] = $queryParamsArray['page_size'];
        }

        return $info;
    }

    /**
     * @param Request $request
     * @param QueryParamsCollection|null $queryParams
     * @param null $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function jsonResponse(
        $data,
        Request $request,
        QueryParamsCollection $queryParams = null,
        $status = 200,
        $headers = array()
    ) {
        $response = array(
            'info' => $this->addAdditionalInfo($request, $queryParams, $headers),
            'data' => $data
        );

        return new JsonResponse($response, $status, $headers);
    }
}
