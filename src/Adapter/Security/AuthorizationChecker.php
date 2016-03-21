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

namespace PrestaShop\PrestaShop\Adapter\Security;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Security\Admin\Employee;

/**
 * Admin Middleware security
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    private $legacyContext;

    /**
     * Constructor.
     *
     * @param LegacyContext $context
     */
    public function __construct(LegacyContext $context)
    {
        $this->legacyContext = $context->getContext();
    }

    /**
     * Check if employee has authorization
     *
     * @param mixed $attributes
     * @param mixed $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        $arrayAttributes = explode(".", $attributes);
        return $this->legacyContext->employee->can($arrayAttributes[0], $arrayAttributes[1]);
    }
}
