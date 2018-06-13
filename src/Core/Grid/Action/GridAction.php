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

namespace PrestaShop\PrestaShop\Core\Grid\Action;

/**
 * Class GridAction is responsible for holding single grid action data
 */
final class GridAction implements GridActionInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var callable|null Custom action renderer
     */
    private $renderer;

    /**
     * @param string $identifier Unique action identifier
     * @param string $name       Translated action name
     * @param string $icon       Action icon name
     */
    public function __construct($identifier, $name, $icon)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->icon = $icon;
    }

    /**
     * Create grid action from array data
     *
     * @param array $data
     *
     * @return GridAction
     */
    public static function fromArray(array $data)
    {
        $action = new GridAction(
            $data['identifier'],
            $data['name'],
            $data['icon']
        );

        if (isset($data['renderer'])) {
            $action->setRenderer($data['renderer']);
        }

        return $action;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return callable|null
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param callable $renderer
     */
    public function setRenderer(callable $renderer)
    {
        $this->renderer = $renderer;
    }
}
