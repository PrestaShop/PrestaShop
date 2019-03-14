<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Routing\Converter;

/**
 * Class LegacyRoute contains the info about a route, its legacyLinks, legacyParameters
 * and controller actions hash map. This class can be built simply based on the routeDefaults
 * parameters and its name.
 */
class LegacyRoute
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var array
     */
    private $legacyLinks;

    /**
     * @var array
     */
    private $routeParameters;

    /**
     * @var array
     */
    private $controllersActions;

    /**
     * @param string|null $action
     *
     * @return bool
     */
    public static function isIndexAction($action)
    {
        $indexAliases = ['list', 'index'];

        return empty($action) || in_array(strtolower($action), $indexAliases);
    }

    /**
     * @param $routeName
     * @param array $routeDefaults
     *
     * @return LegacyRoute
     */
    public static function buildLegacyRoute($routeName, array $routeDefaults)
    {
        $legacyLinks = $routeDefaults['_legacy_link'];
        if (!is_array($legacyLinks)) {
            $legacyLinks = [$legacyLinks];
        }

        $legacyParameters = [];
        if (!empty($routeDefaults['_legacy_parameters']) && is_array($routeDefaults['_legacy_parameters'])) {
            $legacyParameters = $routeDefaults['_legacy_parameters'];
        }

        return new LegacyRoute($routeName, $legacyLinks, $legacyParameters);
    }

    /**
     * LegacyRoute constructor.
     *
     * @param string $routeName
     * @param array $legacyLinks
     * @param array $routeParameters
     */
    public function __construct($routeName, array $legacyLinks, array $routeParameters)
    {
        $this->routeName = $routeName;
        $this->routeParameters = $routeParameters;
        $this->legacyLinks = $this->buildLegacyLinks($legacyLinks);
        $this->controllersActions = $this->buildControllerActions($this->legacyLinks, $routeName);
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return array
     */
    public function getLegacyLinks()
    {
        return $this->legacyLinks;
    }

    /**
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * @return array
     */
    public function getControllersActions()
    {
        return $this->controllersActions;
    }

    /**
     * @param array $legacyLinks
     *
     * @return array
     */
    private function buildLegacyLinks(array $legacyLinks)
    {
        $brokenLegacyLinks = [];
        foreach ($legacyLinks as $legacyLink) {
            $linkParts = explode(':', $legacyLink);
            $legacyController = $linkParts[0];
            $legacyAction = isset($linkParts[1]) ? $linkParts[1] : null;
            $brokenLegacyLinks[] = [
                'controller' => $legacyController,
                'action' => $legacyAction,
            ];
        }

        return $brokenLegacyLinks;
    }

    /**
     * @param array $legacyLinks
     * @param string $routeName
     *
     * @return array
     */
    private function buildControllerActions(array $legacyLinks, $routeName)
    {
        $controllersActions = [];
        foreach ($legacyLinks as $legacyLink) {
            $controller = $legacyLink['controller'];
            if (!isset($controllersActions[$controller])) {
                $controllersActions[$controller] = [];
            }

            $action = self::isIndexAction($legacyLink['action']) ? 'index' : $legacyLink['action'];
            $controllersActions[$controller][$action] = $routeName;
        }

        return $controllersActions;
    }
}
