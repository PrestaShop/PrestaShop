<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CommandHandler\AddCspRuleHandler;
use PrestaShop\PrestaShop\Adapter\Csp\CspRuleValidator;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

class AddCspRuleHandlerTest extends TestCase
{
    public function testItRefusesToAddARuleWithoutASingleShop(): void
    {
        $repository = $this->createMock(CspRuleRepository::class);
        // A rule is strictly per shop; an all-shops/group command must never reach persistence.
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
}
