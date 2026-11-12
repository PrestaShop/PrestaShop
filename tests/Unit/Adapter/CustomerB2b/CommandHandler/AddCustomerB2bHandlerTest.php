<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\CustomerB2b\CommandHandler;

use Doctrine\DBAL\Exception as DBALException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\CustomerB2b\CommandHandler\AddCustomerB2bHandler;
use PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate\CustomerB2bValidationResult;
use PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate\CustomerB2bValidator;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Repository\CustomerB2bRepository;
use Psr\Log\LoggerInterface;

class AddCustomerB2bHandlerTest extends TestCase
{
    private const CUSTOMER_ID = 42;
    private const BUSINESS_ENTITY_ID = 7;
    private const ROLE_ID = 3;

    public function testItLogsAndWrapsSaveException(): void
    {
        $saveFailure = new DBALException('Connection lost');

        $customerB2bRepository = $this->createMock(CustomerB2bRepository::class);
        $customerB2bRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(static fn (CustomerB2b $customerB2b): bool => self::CUSTOMER_ID === $customerB2b->getIdCustomer()))
            ->willThrowException($saveFailure);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('Connection lost'),
                $this->callback(static fn (array $context): bool => self::CUSTOMER_ID === $context['object_id'] && $saveFailure === $context['exception']),
            );

        try {
            $this->buildHandler($customerB2bRepository, $logger)->handle(new AddCustomerB2bCommand(self::CUSTOMER_ID, self::BUSINESS_ENTITY_ID, self::ROLE_ID));
            $this->fail('A CannotAddCustomerB2bException should have been thrown.');
        } catch (CannotAddCustomerB2bException $e) {
            $this->assertSame($saveFailure, $e->getPrevious());
            $this->assertSame(CannotAddCustomerB2bException::FAILED_SAVE, $e->getCode());
        }
    }

    private function buildHandler(CustomerB2bRepository $customerB2bRepository, LoggerInterface $logger): AddCustomerB2bHandler
    {
        $validator = $this->createMock(CustomerB2bValidator::class);
        $validator->method('validate')->willReturn(new CustomerB2bValidationResult(new BusinessEntity(), new B2bRole()));

        return new AddCustomerB2bHandler($validator, $customerB2bRepository, $logger);
    }
}
