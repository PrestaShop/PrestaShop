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
namespace PrestaShop\PrestaShop\tests\Unit\Core\Foundation\Log;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

class MessageStackTest extends UnitTestCase
{
    public function test_message_stack()
    {
        $stackManager = MessageStackManager::getInstance();
        $this->assertAttributeInstanceOf('\\SplQueue', 'errorQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'warningQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'infoQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'successQueue', $stackManager);

        $stackManager->onError(new BaseEvent('test error'));
    }
    // TODO : tests sur une instance, empiler/depiler, tester aussi de declenchement d'un warning et son affichage sur la page, puis une erreur qui donne une page 500 custom avec le détail de l'erreur...
}
