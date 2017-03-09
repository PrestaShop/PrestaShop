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
namespace PrestaShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the Stock pages.
 */
class StockController extends FrameworkBundleAdminController
{
    /**
     * @Template
     *
     * @return array Template vars
     */
    public function overviewAction()
    {
        return [];
    }

    public function hashUpdateJsAction($hash)
    {
        $contents = file_get_contents('http://localhost:8080/' . $hash . '.hot-update.js');

        return new Response($contents);
    }

    public function hashUpdateJsonAction($hash)
    {
        $contents = file_get_contents('http://localhost:8080/' . $hash . '.hot-update.json');

        return new Response($contents);
    }
}
