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

use Exception;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShopBundle\Api\QueryParamsCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class ApiController
{
    protected LoggerInterface $logger;
    protected ContainerInterface $container;
    protected AuthorizationCheckerInterface $authorizationChecker;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param HttpException $exception
     *
     * @return JsonResponse
     */
    protected function handleException(HttpException $exception)
    {
        $this->logger->info($exception->getMessage());

        return new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode());
    }

    /**
     * @param string $content
     *
     * @return array
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
        /** @var SymfonyCacheClearer $cacheClearer */
        $cacheClearer = $this->container->get(SymfonyCacheClearer::class);

        try {
            $cacheClearer->clear();
        } catch (Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }
    }

    /**
     * Add additional info to JSON return.
     *
     * @param Request $request
     * @param QueryParamsCollection|null $queryParams
     * @param array $headers
     *
     * @return array
     */
    protected function addAdditionalInfo(
        Request $request,
        QueryParamsCollection $queryParams = null,
        $headers = []
    ) {
        $router = $this->container->get('router');

        $queryParamsArray = [];
        if (null !== $queryParams) {
            $queryParamsArray = $queryParams->getQueryParams();
        }

        $allParams = $allParamsWithoutPagination = array_merge(
            $request->attributes->get('_route_params'),
            $queryParamsArray,
            $request->query->all()
        );
        unset($allParamsWithoutPagination['page_index'], $allParamsWithoutPagination['page_size']);

        $info = [
            'current_url' => $router->generate($request->attributes->get('_route'), $allParams),
            'current_url_without_pagination' => $router->generate(
                $request->attributes->get('_route'),
                $allParamsWithoutPagination
            ),
        ];

        if (array_key_exists('page_index', $allParams) && $allParams['page_index'] > 1) {
            $previousParams = $allParams;
            if (array_key_exists('page_index', $previousParams)) {
                --$previousParams['page_index'];
            }
            $info['previous_url'] = $router->generate($request->attributes->get('_route'), $previousParams);
        }

        if (array_key_exists('Total-Pages', $headers) &&
            array_key_exists('page_index', $allParams) &&
            $headers['Total-Pages'] > $allParams['page_index']) {
            $nextParams = $allParams;
            if (array_key_exists('page_index', $nextParams)) {
                ++$nextParams['page_index'];
            }
            $info['next_url'] = $router->generate($request->attributes->get('_route'), $nextParams);
        }

        if (array_key_exists('Total-Pages', $headers)) {
            $info['total_page'] = $headers['Total-Pages'];
        }

        if (null !== $queryParams) {
            $info['page_index'] = $queryParamsArray['page_index'];
            $info['page_size'] = $queryParamsArray['page_size'];
        }

        return $info;
    }

    /**
     * @param array $data
     * @param Request $request
     * @param QueryParamsCollection|null $queryParams
     * @param int $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function jsonResponse(
        $data,
        Request $request,
        QueryParamsCollection $queryParams = null,
        $status = 200,
        $headers = []
    ) {
        $response = [
            'info' => $this->addAdditionalInfo($request, $queryParams, $headers),
            'data' => $data,
        ];

        return new JsonResponse($response, $status, $headers);
    }

    /**
     * Checks if access is granted.
     *
     * @param mixed $accessLevel
     * @param string $controller name of the controller
     *
     * @return bool
     */
    protected function isGranted($accessLevel, $controller)
    {
        return $this->authorizationChecker->isGranted(
            $accessLevel,
            $controller . '_'
        );
    }
}
