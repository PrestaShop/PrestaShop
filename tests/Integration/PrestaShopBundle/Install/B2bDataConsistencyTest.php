<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Install;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\DefaultPermission;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\DefaultRole;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\B2bRoleAuthorizationRole;
use PrestaShopBundle\Entity\Employee\AuthorizationRole;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Guards the installation fixtures against the sets of default roles and permissions.
 */
class B2bDataConsistencyTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::bootKernel()->getContainer()->get('doctrine')->getManager();
    }

    public static function getDefaultPermissions(): iterable
    {
        foreach (DefaultPermission::cases() as $permission) {
            yield $permission->name => [$permission];
        }
    }

    public static function getDefaultRoles(): iterable
    {
        foreach (DefaultRole::cases() as $role) {
            yield $role->name => [$role];
        }
    }

    #[DataProvider('getDefaultPermissions')]
    public function testDefaultPermissionIsInstalled(DefaultPermission $permission): void
    {
        $entity = $this->entityManager->getRepository(AuthorizationRole::class)->findOneBy(['slug' => $permission->value]);

        $this->assertNotNull($entity, \sprintf('The "%s" permission was not found in the database.', $permission->value));
    }

    #[DataProvider('getDefaultRoles')]
    public function testDefaultRoleIsInstalledWithItsDefaultPermissions(DefaultRole $role): void
    {
        $entity = $this->entityManager->getRepository(B2bRole::class)->findOneBy(['role' => $role->value]);
        $this->assertNotNull($entity, \sprintf('The "%s" role was not found in the database.', $role->value));

        /** @var DefaultPermission[] $permissions */
        $permissions = array_filter(
            $entity
                ->getB2bRoleAuthorizationRoles()
                ->map(static fn (B2bRoleAuthorizationRole $r) => DefaultPermission::tryFrom($r->getAuthorizationRole()->getSlug()))
                ->toArray(),
        );

        $this->assertEqualsCanonicalizing($role->getDefaultPermissions(), $permissions, \sprintf('The core permissions granted to the "%s" role do not match its defaults.', $role->value));
    }
}
