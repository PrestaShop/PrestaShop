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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Tests\RouterTest\Test;

use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Routing\FakeRouter;
use PrestaShop\PrestaShop\Core\Foundation\Controller\AbstractController;

class RouterTestController extends FrontController
{
    public function aAction(Request &$request, Response &$response)
    {
        $response->setContent('AA');
        return self::RESPONSE_RAW_TEXT;
    }

    public function redirectAction(Request &$request, Response &$response)
    {
        $this->getRouter()->redirect(500);
    }

    public function subcallAction(Request &$request, Response &$response)
    {
        $response->addContentData('sub_a', $this->subcall('fake_controllers_route1', array(), AbstractController::RESPONSE_PARTIAL_VIEW));
    }

    public function forwardAction(Request &$request, Response &$response)
    {
        $this->forward($request, 'fake_controllers_route1');
    }
}
