<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\ControllerAction;

class ControllerActionTest extends TestCase
{
    /**
     * @dataProvider getControllers
     *
     * @param string $fqcn
     * @param array $result
     */
    public function testGetFromString($fqcn, $result)
    {
        $this->assertEquals($result, ControllerAction::fromString($fqcn));
    }

    /**
     * @return array the list of controller names and expected results
     */
    public function getControllers()
    {
        return [
            ['MyNamespace\Foo\Bar\BarController::fooAction', ['bar', 'foo']],
            ['ModuleNameSpace\YoloController::yoloAction', ['yolo', 'yolo']],
            ['PrestaShop\Controller\Admin\ProductController::formAction', ['product', 'form']],
            ['ModuleController', ['module', 'N/A']],
            ['foo::actionAction', ['N/A', 'action']],
            ['This is not even a FQCN', ['N/A', 'N/A']],
        ];
    }
}
