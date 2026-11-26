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
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\EmployeeFormDataHandler;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class EmployeeFormDataHandlerTest extends TestCase
{
    #[DataProvider('twoFactorSubmissions')]
    public function testUpdateOnlySetsSubmittedTwoFactorFields(array $submitted, array $expected): void
    {
        $dispatched = null;
        $bus = $this->createMock(CommandBusInterface::class);
        $bus->expects(self::once())->method('handle')->willReturnCallback(function (EditEmployeeCommand $command) use (&$dispatched): void {
            $dispatched = $command;
        });
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->expects(self::never())->method('loadEmployeeByIdentifier');
        $imageUploader = $this->createMock(ImageUploaderInterface::class);
        $imageUploader->expects(self::never())->method('upload');
        $handler = new EmployeeFormDataHandler(
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

        $handler->update(42, array_merge([
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
