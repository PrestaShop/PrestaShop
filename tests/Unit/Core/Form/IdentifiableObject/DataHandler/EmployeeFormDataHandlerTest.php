<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use Cookie;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\EmployeeFormDataHandler;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class EmployeeFormDataHandlerTest extends TestCase
{
    #[DataProvider('twoFactorSubmissions')]
    public function testUpdateOnlySetsSubmittedTwoFactorFields(array $submitted, array $expected): void
    {
        $dispatched = null;
        $result = new GeneratedEmployeeBackupCodes(42, ['a-new-code']);
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::once())->method('handle')->willReturnCallback(function (EditEmployeeCommand $command) use (&$dispatched, $result): GeneratedEmployeeBackupCodes {
            $dispatched = $command;

            return $result;
        });
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->expects(self::never())->method('loadEmployeeByIdentifier');
        $imageUploader = $this->createMock(ImageUploaderInterface::class);
        $imageUploader->expects(self::never())->method('upload');
        $handler = $this->createHandler($bus, $imageUploader, $repository);

        $actual = $handler->update(42, array_merge([
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
            'email' => 'ada@example.com',
            'default_page' => 1,
            'language' => 1,
            'active' => true,
            'profile' => 2,
            'has_enabled_gravatar' => true,
            'avatarUrl' => null,
        ], $submitted));

        self::assertSame($result, $actual);
        self::assertInstanceOf(EditEmployeeCommand::class, $dispatched);
        self::assertSame(42, $dispatched->getEmployeeId()->getValue());
        self::assertSame('Lovelace', $dispatched->getLastName()->getValue());
        self::assertSame($expected, [
            $dispatched->getTwoFactorEnabled(),
            $dispatched->getTwoFactorTotpEnabled(),
            $dispatched->getTwoFactorEmailEnabled(),
            $dispatched->getTwoFactorRequired(),
        ]);
    }

    public function testResultIsReturnedOnlyAfterAvatarUploadSucceeds(): void
    {
        $result = new GeneratedEmployeeBackupCodes(42, ['new-code']);
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->method('handle')->willReturn($result);
        $uploader = $this->createMock(ImageUploaderInterface::class);
        $avatar = $this->createMock(UploadedFile::class);
        $uploader->expects(self::once())->method('upload')->with(42, $avatar)
            ->willThrowException(new RuntimeException('Avatar upload failed'));
        $handler = $this->createHandler($bus, $uploader, $this->createMock(EmployeeRepository::class));

        $this->expectException(RuntimeException::class);
        $handler->update(42, [
            'firstname' => 'Ada', 'lastname' => 'Lovelace', 'email' => 'ada@example.com',
            'default_page' => 1, 'language' => 1, 'active' => true, 'profile' => 2,
            'has_enabled_gravatar' => true, 'avatarUrl' => $avatar,
        ]);
    }

    private function createHandler(CommandBusInterface $bus, ImageUploaderInterface $imageUploader, EmployeeRepository $repository): EmployeeFormDataHandler
    {
        return new EmployeeFormDataHandler(
            $bus,
            [1],
            1,
            $this->createMock(EmployeeFormAccessCheckerInterface::class),
            $this->createMock(EmployeeDataProviderInterface::class),
            $this->createMock(Hashing::class),
            $imageUploader,
            1,
            72,
            1,
            false,
            $this->createMock(Cookie::class),
            new EmployeeContext(null, []),
            $this->createMock(TokenStorageInterface::class),
            $repository,
            $this->createMock(UserTokenManager::class),
            $this->createMock(CsrfTokenManagerInterface::class)
        );
    }

    public static function twoFactorSubmissions(): iterable
    {
        yield 'all fields absent' => [[], [null, null, null, null]];
        yield 'explicitly disable all fields' => [[
            'two_factor_enabled' => false,
            'two_factor_totp_enabled' => false,
            'two_factor_email_enabled' => false,
            'two_factor_required' => false,
        ], [false, false, false, false]];
        yield 'explicitly enable all fields' => [[
            'two_factor_enabled' => true,
            'two_factor_totp_enabled' => true,
            'two_factor_email_enabled' => true,
            'two_factor_required' => true,
        ], [true, true, true, true]];
        yield 'only disable 2FA' => [['two_factor_enabled' => false], [false, null, null, null]];
        yield 'only disable TOTP' => [['two_factor_totp_enabled' => false], [null, false, null, null]];
        yield 'only enable TOTP' => [['two_factor_totp_enabled' => true], [null, true, null, null]];
        yield 'only disable email' => [['two_factor_email_enabled' => false], [null, null, false, null]];
        yield 'only change requirement' => [['two_factor_required' => false], [null, null, null, false]];
    }
}
