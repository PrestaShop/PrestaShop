<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Business\Controller\ExecutionSequenceService;

use PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceWrapper;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Business\Routing\RoutingService;

/**
 * This middleware interface brings 2 methods to allow authentication process.
 */
interface AuthenticationMiddlewareInterface
{
    /**
     * Checks if the user is authenticated.
     *
     * @return boolean True if the user is authenticated.
     */
    public function isAuthenticated();

    /**
     * Return the URL to use if the user is not authenticated.
     *
     * The URL must brings the user to a way to authenticate.
     *
     * @return string The URL to redirect the user to.
     */
    public function getAuthenticationUrl();
}
