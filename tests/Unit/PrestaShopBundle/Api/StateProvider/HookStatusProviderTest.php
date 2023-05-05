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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Api\StateProvider;

use ApiPlatform\Metadata\Get;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShopBundle\Api\Provider\QueryProvider;
use RuntimeException;

class HookStatusProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * Set up dependencies for HookStatusProvider
     */
    public function setUp(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetHookStatus::class))
            ->willReturnCallback(function ($query) {
                return $this->createResultBasedOnQuery($query);
            })
        ;
    }

    private function createResultBasedOnQuery(GetHookStatus $query): HookStatus
    {
        if ($query->getHookId()->getValue() === 1) {
            return new HookStatus($query->getHookId()->getValue(), false);
        }

        if ($query->getHookId()->getValue() === 2) {
            return new HookStatus($query->getHookId()->getValue(), true);
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getHookId()->getValue()));
    }

    public function testProvideHookStatus(): void
    {
        $hookStatusProvider = new QueryProvider($this->queryBus);
        $get = new Get();
        $get = $get->withExtraProperties(['query' => "PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus"]);
        // @phpstan-ignore-next-line
        self::assertEquals(false, $hookStatusProvider->provide($get, ['id' => 1])->isActive());
        // @phpstan-ignore-next-line
        self::assertTrue($hookStatusProvider->provide($get, ['id' => 2])->isActive());
    }
}
