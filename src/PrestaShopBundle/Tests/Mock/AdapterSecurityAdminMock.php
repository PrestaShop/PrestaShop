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

namespace PrestaShopBundle\Tests\Mock;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Admin Middleware security
 */
class AdapterSecurityAdminMock
{
    public function __construct(LegacyContext $context, TokenStorage $securityTokenStorage)
    {
    }

    /**
     * Check if employee is logged in
     * If not loggedin in, redirect to admin home page
     *
     * @param GetResponseEvent $event
     *
     * @return bool or redirect
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
    }
}
