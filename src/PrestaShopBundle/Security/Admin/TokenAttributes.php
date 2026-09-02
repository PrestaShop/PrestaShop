<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

class TokenAttributes
{
    /**
     * Used to store the IP address as an integer to compare if it changed (to avoid copying/stealing the cookie session)
     */
    public const IP_ADDRESS = '_ip_address';

    /**
     * Used to store the serialized EmployeeSession object which is then used to check if is still valid (in the DB).
     */
    public const EMPLOYEE_SESSION = '_employee_session';

    /**
     * Used to store a ShopConstraint object that represents the current Shop context (All shops, single shop, shop group).
     */
    public const SHOP_CONSTRAINT = '_shop_constraint';
}
