<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\BusinessEntity;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessEntitiesController manages the "Sell > Business Entities" page.
 */
class BusinessEntitiesController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminBusinessEntities')")]
    public function listAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/BusinessEntity/list.html.twig');
    }
}
