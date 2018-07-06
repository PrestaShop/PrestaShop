<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\Routing\RouterInterface;

/**
 * @todo: add unit tests
 */
class ActionButton implements ActionButtonInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $routeName;

    /**
     * Used to match row items with route parameters
     *
     * @var array
     */
    private $routeParametersMapping;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var bool
     */
    private $needConfirmation;

    /**
     * @param RouterInterface $router
     * @param string $routeName
     * @param array $routeParametersMapping
     * @param string $title
     * @param string $icon
     * @param bool $needConfirmation
     */
    public function __construct(RouterInterface $router, $routeName, array $routeParametersMapping, $title, $icon, $needConfirmation = false)
    {
        $this->router = $router;
        $this->routeName = $routeName;
        $this->routeParametersMapping = $routeParametersMapping;
        $this->title = $title;
        $this->icon = $icon;
        $this->needConfirmation = $needConfirmation;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink(array $row)
    {
        $routeParameters = $this->extractRouteParametersFromRow($row);

        return $this->router->generate($this->routeName, $routeParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * {@inheritdoc}
     */
    public function needsConfirmation()
    {
        return $this->needConfirmation;
    }

    /**
     * @param array $row
     *
     * @return array
     *
     * @throws ColumnDefinitionException if given $row does not have mapping parameters
     */
    private function extractRouteParametersFromRow(array $row)
    {
        $routeParameters = [];

        foreach ($this->routeParametersMapping as $routeParameterName => $propertyName) {

            if (false === array_key_exists($propertyName, $row)) {
                throw new ColumnDefinitionException(sprintf(
                        'ActionButton expects row with property %s to build link for route %s, available properties are %s',
                        $propertyName,
                        $this->routeName,
                        implode(', ', array_keys($row))
                    )
                );
            }

            $routeParameters[$routeParameterName] = $row[$propertyName];
        }

        return $routeParameters;
    }
}