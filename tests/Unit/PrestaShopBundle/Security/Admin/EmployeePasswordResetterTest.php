<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Security\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Routing\AdminUrlGenerator;
use PrestaShopBundle\Security\Admin\EmployeePasswordResetter;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeePasswordResetterTest extends TestCase
{
    private const TOKEN = '0123456789abcdef0123456789abcdef01234567';

    public function testSendResetEmailStoresARandomTokenOf40HexCharacters(): void
    {
        $employee = (new Employee())
            ->setEmail('employee@example.com')
            ->setPasswordLastGeneration(new DateTime('-1 day'));
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('loadEmployeeByIdentifier')->willReturn($employee);
        $urlGenerator = $this->createMock(AdminUrlGenerator::class);
        // Stops the call before Mail::Send(), once the token is stored and the reset URL built from it
        $urlGenerator->method('generateAdminUrl')->willThrowException(new RuntimeException('mail not sent in this test'));

        $tokens = [];
        for ($i = 0; $i < 2; ++$i) {
            try {
                $this->createResetter($repository, $urlGenerator)->sendResetEmail('employee@example.com');
            } catch (RuntimeException) {
            }
            $tokens[] = $employee->getResetPasswordToken();
        }

        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', (string) $tokens[0]);
        $this->assertNotSame($tokens[0], $tokens[1]);
    }

    public function testGetEmployeeByValidResetPasswordTokenReturnsTheEmployeeOfThatToken(): void
    {
        $employee = $this->createEmployeeWithToken(self::TOKEN, new DateTime('+1 hour'));

        $this->assertSame($employee, $this->createResetterFinding($employee)->getEmployeeByValidResetPasswordToken(self::TOKEN));
    }

    public function testGetEmployeeByValidResetPasswordTokenRefusesAnotherToken(): void
    {
        // A case-insensitive collation can match the row on a token that is not the stored one
        $employee = $this->createEmployeeWithToken(self::TOKEN, new DateTime('+1 hour'));

        $this->assertNull($this->createResetterFinding($employee)->getEmployeeByValidResetPasswordToken(strtoupper(self::TOKEN)));
    }

    public function testGetEmployeeByValidResetPasswordTokenRefusesAnExpiredToken(): void
    {
        $employee = $this->createEmployeeWithToken(self::TOKEN, new DateTime('-1 hour'));

        $this->assertNull($this->createResetterFinding($employee)->getEmployeeByValidResetPasswordToken(self::TOKEN));
    }

    private function createEmployeeWithToken(string $token, DateTime $validity): Employee
    {
        return (new Employee())
            ->setResetPasswordToken($token)
            ->setResetPasswordValidity($validity);
    }

    private function createResetterFinding(Employee $employee): EmployeePasswordResetter
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('findOneBy')->willReturn($employee);

        return $this->createResetter($repository, $this->createMock(AdminUrlGenerator::class));
    }

    private function createResetter(EmployeeRepository $repository, AdminUrlGenerator $urlGenerator): EmployeePasswordResetter
    {
        return new EmployeePasswordResetter(
            $repository,
            $this->createMock(ConfigurationInterface::class),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(TranslatorInterface::class),
            $urlGenerator,
            $this->createMock(Hashing::class),
            'cookie_key'
        );
    }
}
