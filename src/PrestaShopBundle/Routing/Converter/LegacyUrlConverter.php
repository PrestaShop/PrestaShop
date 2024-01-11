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

namespace PrestaShopBundle\Routing\Converter;

use PrestaShopBundle\Routing\Converter\Exception\AlreadyConvertedException;
use PrestaShopBundle\Routing\Converter\Exception\ArgumentException;
use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class LegacyUrlConverter is able to convert query parameters or an url into a
 * migrated Symfony url. It uses information set in the routes via _legacy_link
 * to do so.
 */
final class LegacyUrlConverter
{
    /**
     * @var LegacyRouteProviderInterface
     */
    private $legacyRouteProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * LegacyUrlConverter constructor.
     *
     * @param RouterInterface $router
     * @param LegacyRouteProviderInterface $legacyRouteProvider
     */
    public function __construct(RouterInterface $router, LegacyRouteProviderInterface $legacyRouteProvider)
    {
        $this->router = $router;
        $this->legacyRouteProvider = $legacyRouteProvider;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function convertByParameters(array $parameters)
    {
        if (empty($parameters['controller'])) {
            throw new ArgumentException('Missing required controller argument');
        }

        /** @var LegacyRoute $legacyRoute */
        $legacyRoute = $this->findLegacyRouteNameByParameters($parameters);
        $parameters = $this->convertLegacyParameters($parameters, $legacyRoute);

        return $this->router->generate($legacyRoute->getRouteName(), $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $url
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     * @throws AlreadyConvertedException
     */
    public function convertByUrl($url)
    {
        $this->checkAlreadyMatchingRoute($url);

        $parsedUrl = parse_url($url);
        $parameters = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parameters);
        }

        return $this->convertByParameters($parameters);
    }

    /**
     * This conversion method is used by the listener, indeed it needs to update the
     * router context because it is executed before the RouterListener. Thus the router
     * does not have the appropriate information to generate an admin url and would redirect
     * to the front office.
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     * @throws AlreadyConvertedException
     */
    public function convertByRequest(Request $request)
    {
        $this->router->getContext()->fromRequest($request);
        $this->checkAlreadyMatchingRoute($request->getRequestUri());

        $parameters = array_merge($request->query->all(), $request->request->all());

        return $this->convertByParameters($parameters);
    }

    /**
     * @param array $parameters
     * @param LegacyRoute $legacyRoute
     *
     * @return array
     */
    private function convertLegacyParameters(array $parameters, LegacyRoute $legacyRoute)
    {
        $legacyAction = $this->getActionFromParameters($parameters);

        foreach ($legacyRoute->getRouteParameters() as $legacyParameter => $parameter) {
            if (isset($parameters[$legacyParameter])) {
                $parameters[$parameter] = $parameters[$legacyParameter];
                unset($parameters[$legacyParameter]);
            }
        }

        unset(
            $parameters['controller'],
            $parameters['action'],
            $parameters[$legacyAction]
        );

        return $parameters;
    }

    /**
     * @param array $parameters
     *
     * @return LegacyRoute
     *
     * @throws RouteNotFoundException
     */
    private function findLegacyRouteNameByParameters(array $parameters)
    {
        $legacyController = $parameters['controller'];
        $legacyAction = $this->getActionFromParameters($parameters);

        return $this->legacyRouteProvider->getLegacyRouteByAction($legacyController, $legacyAction);
    }

    /**
     * @param array $parameters
     *
     * @return string|null
     */
    private function getActionFromParameters(array $parameters)
    {
        $legacyAction = null;

        if (!empty($parameters['action'])) {
            $legacyAction = $parameters['action'];
        }

        //Actions can be defined as simple query parameter (e.g: ?controller=AdminProducts&save)
        if (null === $legacyAction) {
            //We prioritize the actions defined in the migrated routes
            $controllerActions = $this->legacyRouteProvider->getActionsByController($parameters['controller']);
            foreach ($parameters as $parameter => $value) {
                if (in_array($parameter, $controllerActions)) {
                    $legacyAction = $parameter;

                    break;
                }
            }
        }

        //Last chance if a non migrated action is present (note: a bit risky since any empty parameter can be
        //interpreted as an action.. but some old link need this feature, ?controller=AdminModulesPositions&addToHook)
        if (null === $legacyAction) {
            foreach ($parameters as $parameter => $value) {
                if ($value === '' || $value === '1' || $value === 1) {
                    //Avoid confusing an entity/row id with an action
                    // e.g.
                    //  create=1 is an action
                    //  id_product=1 is NOT an action
                    if (!str_contains($parameter, 'id_')
                        && !str_contains($parameter, '_id')) {
                        $legacyAction = $parameter;

                        break;
                    }
                }
            }
        }

        return LegacyRoute::isIndexAction($legacyAction) ? 'index' : $legacyAction;
    }

    /**
     * We check that the router doesn't match the url, if no exception is thrown
     * then the url is already converted and we throw our own AlreadyConvertedException
     *
     * @param string $url
     *
     * @throws AlreadyConvertedException
     */
    private function checkAlreadyMatchingRoute($url)
    {
        try {
            $urlPath = parse_url($url, PHP_URL_PATH);
            if (!empty($urlPath)) {
                $this->router->match($urlPath);
                throw new AlreadyConvertedException(sprintf('%s is already a converted url', $url));
            }
        } catch (ExceptionInterface $e) {
        }
    }
}
