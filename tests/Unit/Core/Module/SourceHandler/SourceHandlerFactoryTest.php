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

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerNotFoundException;

class SourceHandlerFactoryTest extends TestCase
{
    /** @var SourceHandlerFactory */
    private $sourceHandlerFactory;

    public function setUp(): void
    {
        $this->sourceHandlerFactory = new SourceHandlerFactory();
    }

    public function testGetUnavailableHandler()
    {
        $this->expectException(SourceHandlerNotFoundException::class);
        $this->sourceHandlerFactory->getHandler('unhandlablesource');
    }

    public function testGetHandler()
    {
        $handlerMock = $this->createMock(SourceHandlerInterface::class);
        $handlerMock->method('canHandle')->willReturn(true);

        $this->sourceHandlerFactory->addHandler($handlerMock);

        $this->assertEquals($handlerMock, $this->sourceHandlerFactory->getHandler('handlablesource'));
    }
}
