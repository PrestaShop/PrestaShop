<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Security;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Security\FormDataProvider;

class FormDataProviderTest extends TestCase
{
    /**
     * @var DataConfigurationInterface|MockObject
     */
    protected $config;

    /**
     * @var FormDataProvider
     */
    protected $object;

    protected function setUp(): void
    {
        $this->config = $this->getMockBuilder(DataConfigurationInterface::class)
            ->onlyMethods([
                'getConfiguration',
                'updateConfiguration',
                'validateConfiguration',
            ])
            ->getMock();

        $this->object = new FormDataProvider($this->config);
    }

    public function testGetConfigurationIsCalledOnlyOnce(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([]);

        $this->assertEquals([], $this->object->getData());
    }

    public function testUpdateConfigurationIsCalledOnlyOnce(): void
    {
        $this->config
            ->expects($this->once())
            ->method('updateConfiguration')
            ->with(['this', 'is', 'sparta'])
            ->willReturn([]);

        $this->assertEquals([], $this->object->setData(['this', 'is', 'sparta']));
    }
}
