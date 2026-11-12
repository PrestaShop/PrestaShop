<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\DefaultRole;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleException;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bException;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use PrestaShopBundle\Entity\Repository\CustomerB2bRepository;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CustomerB2bFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * The table accepts the business entity by name ("business_entity") and the role by code ("role"), or raw ids
     * ("business_entity_id", "role_id") to target records that do not exist.
     *
     * @When I add a B2B profile to customer :customerReference with the following details:
     */
    public function addCustomerB2b(string $customerReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $this->handleAddCustomerB2b(
            $customerReference,
            isset($data['business_entity_id']) ? (int) $data['business_entity_id'] : $this->getBusinessEntityByName($data['business_entity'])->getId(),
            isset($data['role_id']) ? (int) $data['role_id'] : $this->getRoleByCode($data['role'])->getIdRole(),
            isset($data['status']) ? CustomerB2bStatus::from($data['status']) : null,
            $data['external_ref'] ?? null,
        );
    }

    /**
     * @When I add a B2B profile to customer :customerReference in business entity :businessEntityName with an external reference of :length characters
     */
    public function addCustomerB2bWithExternalRefOfLength(string $customerReference, string $businessEntityName, int $length): void
    {
        $this->handleAddCustomerB2b(
            $customerReference,
            $this->getBusinessEntityByName($businessEntityName)->getId(),
            $this->getRoleByCode(DefaultRole::BUYER->value)->getIdRole(),
            null,
            str_repeat('a', $length),
        );
    }

    /**
     * @Then customer :customerReference should have a B2B profile with the following details:
     */
    public function assertCustomerB2b(string $customerReference, TableNode $table): void
    {
        $customerB2b = $this->findCustomerB2b($customerReference);
        Assert::assertNotNull($customerB2b, \sprintf('Customer "%s" has no B2B profile', $customerReference));

        $data = $table->getRowsHash();
        if (isset($data['status'])) {
            Assert::assertSame(CustomerB2bStatus::from($data['status']), $customerB2b->getStatus());
        }
        if (array_key_exists('external_ref', $data)) {
            Assert::assertSame('' === $data['external_ref'] ? null : $data['external_ref'], $customerB2b->getExternalRef());
        }
        if (isset($data['business_entity'])) {
            $link = $this->getLinkToBusinessEntity($customerB2b, $data['business_entity']);
            if (isset($data['role'])) {
                Assert::assertSame($data['role'], $link->getB2bRole()->getRole());
            }
            if (isset($data['is_default'])) {
                Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_default']), $link->isDefault());
            }
        }
    }

    /**
     * @Then customer :customerReference should not have a B2B profile
     */
    public function assertCustomerHasNoCustomerB2b(string $customerReference): void
    {
        Assert::assertNull($this->findCustomerB2b($customerReference), \sprintf('Customer "%s" should not have a B2B profile', $customerReference));
    }

    /**
     * @Then the creation of the B2B profile of customer :customerReference should be logged
     */
    public function assertCustomerB2bCreationIsLogged(string $customerReference): void
    {
        $customerB2b = $this->findCustomerB2b($customerReference);
        Assert::assertNotNull($customerB2b, \sprintf('Customer "%s" has no B2B profile', $customerReference));

        $connection = $this->getEntityManager()->getConnection();
        $logCount = $connection
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(_DB_PREFIX_ . 'log', 'l')
            ->where('l.object_type = :objectType')
            ->andWhere('l.object_id = :objectId')
            ->andWhere('l.message LIKE :message')
            ->setParameter('objectType', 'CustomerB2b')
            ->setParameter('objectId', $customerB2b->getId())
            ->setParameter('message', 'B2B customer created%')
            ->executeQuery()
            ->fetchOne();

        Assert::assertSame(1, (int) $logCount);
    }

    /**
     * @Then I should get an error that the customer already has a B2B profile
     */
    public function assertLastErrorIsCustomerB2bAlreadyExists(): void
    {
        $this->assertLastErrorIs(CustomerB2bAlreadyExistsException::class);
    }

    /**
     * @Then /^I should get an error that a (guest|deleted) customer cannot have a B2B profile$/
     */
    public function assertLastErrorIsIneligibleCustomer(string $reason): void
    {
        $this->assertLastErrorIs(
            CannotAddCustomerB2bException::class,
            'guest' === $reason ? CannotAddCustomerB2bException::GUEST_CUSTOMER : CannotAddCustomerB2bException::DELETED_CUSTOMER,
        );
    }

    /**
     * @Then I should get an error that the B2B role was not found
     */
    public function assertLastErrorIsB2bRoleNotFound(): void
    {
        $this->assertLastErrorIs(B2bRoleNotFoundException::class);
    }

    /**
     * @Then I should get an error that the B2B profile external reference is invalid
     */
    public function assertLastErrorIsInvalidExternalRef(): void
    {
        $this->assertLastErrorIs(CustomerB2bConstraintException::class, CustomerB2bConstraintException::INVALID_EXTERNAL_REF);
    }

    private function handleAddCustomerB2b(
        string $customerReference,
        int $businessEntityId,
        int $roleId,
        ?CustomerB2bStatus $status,
        ?string $externalRef,
    ): void {
        try {
            $this->getCommandBus()->handle(new AddCustomerB2bCommand(
                $this->referenceToId($customerReference),
                $businessEntityId,
                $roleId,
                $status,
                $externalRef,
            ));
        } catch (CustomerB2bException|CustomerException|BusinessEntityException|B2bRoleException $e) {
            $this->setLastException($e);
        }
    }

    private function findCustomerB2b(string $customerReference): ?CustomerB2b
    {
        /** @var CustomerB2bRepository $repository */
        $repository = $this->getEntityManager()->getRepository(CustomerB2b::class);

        return $repository->findByCustomerId($this->referenceToId($customerReference));
    }

    private function getLinkToBusinessEntity(CustomerB2b $customerB2b, string $businessEntityName): BusinessEntityCustomerB2b
    {
        foreach ($customerB2b->getBusinessEntityCustomerB2bs() as $link) {
            if ($link->getBusinessEntity()->getName() === $businessEntityName) {
                return $link;
            }
        }

        Assert::fail(\sprintf('The B2B profile is not linked to business entity "%s"', $businessEntityName));
    }

    private function getBusinessEntityByName(string $name): BusinessEntity
    {
        $businessEntity = $this->getEntityManager()->getRepository(BusinessEntity::class)->findOneBy(['name' => $name]);
        Assert::assertNotNull($businessEntity, \sprintf('Business entity "%s" not found', $name));

        return $businessEntity;
    }

    private function getRoleByCode(string $code): B2bRole
    {
        $role = $this->getEntityManager()->getRepository(B2bRole::class)->findOneBy(['role' => $code]);
        Assert::assertNotNull($role, \sprintf('B2B role "%s" not found', $code));

        return $role;
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
