<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

class AdapterServiceLocatorTest extends TestCase
{
    /**
     * @var Container
     */
    private $savedContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedContainer = ServiceLocator::getContainer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        ServiceLocator::setServiceContainerInstance($this->savedContainer);
    }

    public function testGetDelegatesToServiceContainer(): void
    {
        ServiceLocator::setServiceContainerInstance(new Container());

        $this->assertInstanceOf(Container::class, ServiceLocator::get(Container::class));
    }
}
