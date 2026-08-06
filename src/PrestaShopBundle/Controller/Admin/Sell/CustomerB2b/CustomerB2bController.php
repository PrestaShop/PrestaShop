<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerB2b;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerB2bController manages the "Sell > Customers B2B" page.
 */
class CustomerB2bController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminCustomersB2b')")]
    public function listAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/CustomerB2b/list.html.twig');
    }
}
