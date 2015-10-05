<?php
namespace PrestaShop\PrestaShop\Tests\Fake;

use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;

class FakeRouter extends Router
{
    public function __construct(Container $container)
    {
        parent::__construct($container, 'fake_test_routes(_(.*))?\.yml');
    }
    public $calledcheckControllerAuthority = null;
    protected function checkControllerAuthority(\ReflectionClass $class)
    {
        $this->calledcheckControllerAuthority = $class->name;
        if ($class->name == 'PrestaShop\\PrestaShop\\Tests\\RouterTest\\Test\\RouterTestControllerError') {
            throw new \ErrorException('FakeControllerError stops!');
        }
        if ($class->name == 'PrestaShop\\PrestaShop\\Tests\\RouterTest\\Test\\RouterTestControllerWarning') {
            throw new WarningException('FakeControllerWarning does not stop!', 'alternateText');
        }
    }

    public static $calledExitNow = false;
    public function exitNow($i = 0)
    {
        self::$calledExitNow = true;
    }
}

class FakeControllerError extends FrontController
{
}
class FakeControllerWarning extends FrontController
{
}
