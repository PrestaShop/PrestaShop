<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Inspired by \Symfony\Component\HttpFoundation\RequestStack
 */
class ControllerStack
{
    /**
     * @var Controller[]
     */
    private $controllers = [];

    /**
     * @param Controller $controller
     */
    public function push(Controller $controller)
    {
        $this->controllers[] = $controller;
    }

    /**
     * @return Controller|null
     */
    public function pop()
    {
        if (!$this->controllers) {
            return null;
        }

        return array_pop($this->controllers);
    }

    /**
     * @return Controller|null
     */
    public function getCurrentController()
    {
        return end($this->controllers) ?: null;
    }

    /**
     * Gets the master Controller.
     *
     * @return Controller|null
     */
    public function getRootController()
    {
        if (!$this->controllers) {
            return null;
        }

        return $this->controllers[0];
    }

    /**
     * Returns the parent request of the current.
     *
     * If current Controller is the master controller, it returns null.
     *
     * @return Controller|null
     */
    public function getParentController()
    {
        $pos = \count($this->controllers) - 2;

        if (!isset($this->controllers[$pos])) {
            return null;
        }

        return $this->controllers[$pos];
    }
}
