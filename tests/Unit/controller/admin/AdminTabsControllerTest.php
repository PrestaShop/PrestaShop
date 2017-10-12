<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace Prestashop\Prestashop\Tests\Unit\Controller\Admin;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Phake;
use Tools;
use Tab;
use DbQuery;
use AdminTabsController;

class AdminTabsControllerTest extends UnitTestCase
{
    private $controller;

    public function setUp()
    {
        parent::setUp();

        $this->controller = new AdminTabsController();
    }

    /**
     * @group admin
     */
    public function testRenderDetails()
    {
        $request = Phake::mock('\Symfony\Component\HttpFoundation\Request');
        $queryBag = Phake::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $requestBag = Phake::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $request->query = $queryBag;
        $request->request = $requestBag;

        $tabId = 1;
        Phake::when($queryBag)->get('id_tab', false)->thenReturn($tabId);
        Phake::when($requestBag)->get('id_tab', $tabId)->thenReturn($tabId);

        $this->tools = new Tools($request);

        $tab = new Tab();
        $tab->id = $tabId;
        $this->entity_mapper->willReturn($tab)->forId($tabId);
        $language = $this->setupContextualLanguageMock();
        $language->id = 1;

        $this->controller->renderDetails();
    }

    protected function setupContextualCookieMock()
    {
        $cookieMock = $this->getMockBuilder('\Cookie')
            ->disableOriginalConstructor()
            ->setMethods(array('getFamily'))
            ->getMock();

        $cookieMock->expects($this->once())
            ->method('getFamily')
            ->with($this->anything())
            ->willReturn(array());

        $this->context->cookie = $cookieMock;
    }

    public function setupDatabaseMock($mock = null)
    {
        $dbMock = $this->getMockBuilder('\DbPDO')
            ->disableOriginalConstructor()
            ->setMethods(array('query', 'executeS', 'getMsgError'))
            ->getMock();

        $dbMock->expects($this->any())
            ->method('query')
            ->with($this->callback(function ($subject) {
                // It should check if multi-shop is active
                return strpos($subject, 'PS_MULTISHOP_FEATURE_ACTIVE') !== false;
            }));

        $dbMock->expects($this->any())
            ->method('executeS')
            ->with($this->callback(function ($subject) {
                if ($subject instanceof DbQuery) {
                    $builtQuery = $subject->build();

                    // It should select modules
                    return strpos($builtQuery, 'module') !== false;

                }

                // It should select tabs
                return strpos($subject, 'tab') !== false ||
                    // It should select authorization
                    strpos($subject, 'authorization') !== false ||
                    strpos($subject, 'ps_configuration') !== false ||
                    strpos($subject, 'ps_shop') !== false ||
                    // It should select hook alias
                    strpos($subject, 'hook_alias') !== false;

            }))
            ->will($this->returnCallback(function ($subject) {
                if (strpos($subject, 'authorization') !== false) {
                    return array();
                } else {
                    return false;
                }
            }));

        parent::setupDatabaseMock($dbMock);
    }

    public static function tearDownAfterClass() {
        Tools::resetRequest();
    }
}
