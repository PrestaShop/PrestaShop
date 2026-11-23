<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Security\Voter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\OAuth2\JwtTokenUser;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\Profile;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PageVoterTest extends TestCase
{
    public function testEmployeeIsGrantedFromItsProfile(): void
    {
        $profile = $this->createMock(Profile::class);
        $profile->method('getId')->willReturn(4);
        $employee = $this->createMock(Employee::class);
        $employee->method('getProfile')->willReturn($profile);
        $employee->method('getRoles')->willReturn([]);

        $accessChecker = $this->createMock(AccessCheckerInterface::class);
        $accessChecker->expects($this->once())
            ->method('isEmployeeGranted')
            ->with('AdminAccess_' . Permission::UPDATE, 4)
            ->willReturn(true);

        $voter = new PageVoter($accessChecker);
        $token = new UsernamePasswordToken($employee, 'admin', []);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, 'AdminAccess', [Permission::UPDATE]));
    }

    public function testAdminApiClientIsDenied(): void
    {
        // An Admin API client is a JwtTokenUser, it has no employee profile
        $accessChecker = $this->createMock(AccessCheckerInterface::class);
        $accessChecker->expects($this->never())->method('isEmployeeGranted');

        $voter = new PageVoter($accessChecker);
        $user = new JwtTokenUser('client', []);
        $token = new UsernamePasswordToken($user, 'admin_api', $user->getRoles());

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, 'AdminAccess', [Permission::UPDATE]));
    }
}
