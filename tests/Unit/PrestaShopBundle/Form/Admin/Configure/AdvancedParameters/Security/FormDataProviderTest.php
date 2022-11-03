<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            ->setMethods([
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
