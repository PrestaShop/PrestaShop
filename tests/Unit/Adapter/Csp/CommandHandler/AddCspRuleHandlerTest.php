<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp\CommandHandler;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CommandHandler\AddCspRuleHandler;
use PrestaShop\PrestaShop\Adapter\Csp\CspRuleValidator;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

class AddCspRuleHandlerTest extends TestCase
{
    public function testItRefusesToAddAStorefrontRuleWithoutASingleShop(): void
    {
        $repository = $this->createMock(CspRuleRepository::class);
        // A storefront rule is per shop; an all-shops/group command must never reach persistence.
        $repository->expects($this->never())->method('add');

        // CspRuleValidator is final and is not reached on the all-shops path, so a real instance over a
        // mocked repository is enough.
        $handler = new AddCspRuleHandler(
            $repository,
            $this->createMock(CspLogRepository::class),
            new CspRuleValidator($repository),
            $this->createMock(EntityManagerInterface::class)
        );

        $this->expectException(CannotAddCspRuleException::class);

        $handler->handle(new AddCspRuleCommand('script-src', 'https://cdn.example.com', ShopConstraint::allShops()));
    }

    public function testItAddsABackOfficeRuleUnderShopZeroWithoutASingleShop(): void
    {
        // The back office is a single global surface, so an admin rule is accepted with no single shop.
        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('findOneByShopDirectiveSource')->willReturn(null);
        $repository->expects($this->once())->method('add')->willReturn(9);

        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->expects($this->once())->method('insertPlaceholderIfAbsent')->with(CspContext::ADMIN, 0, 'script-src', 'https://admin.example.com');

        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(fn (callable $operation) => $operation());
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willReturn($connection);

        $handler = new AddCspRuleHandler($repository, $logRepository, new CspRuleValidator($repository), $entityManager);

        $ruleId = $handler->handle(new AddCspRuleCommand('script-src', 'https://admin.example.com', ShopConstraint::allShops(), CspContext::ADMIN));

        $this->assertSame(9, $ruleId->getValue());
    }
}
