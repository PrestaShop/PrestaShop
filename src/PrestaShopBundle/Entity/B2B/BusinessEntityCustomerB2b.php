<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessEntityCustomerB2b.
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="business_entity_customer_b2b_be_idx", columns={"id_business_entity"}),
 *         @ORM\Index(name="business_entity_customer_b2b_customer_idx", columns={"id_customer_b2b"}),
 *         @ORM\Index(name="business_entity_customer_b2b_role_idx", columns={"id_role_b2b"})
 *     },
 *     uniqueConstraints={
 *
 *         @ORM\UniqueConstraint(name="uniq_be_customer", columns={"id_business_entity", "id_customer_b2b"})
 *     }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity()
 */
class BusinessEntityCustomerB2b
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_business_entity_customer_b2b", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntity", inversedBy="businessEntityCustomerB2bs")
     *
     * @ORM\JoinColumn(name="id_business_entity", referencedColumnName="id_business_entity", nullable=false)
     */
    private BusinessEntity $businessEntity;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\CustomerB2b", inversedBy="businessEntityCustomerB2bs")
     *
     * @ORM\JoinColumn(name="id_customer_b2b", referencedColumnName="id_customer_b2b", nullable=false)
     */
    private CustomerB2b $customerB2b;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\B2bRole", inversedBy="businessEntityCustomerB2bs")
     *
     * @ORM\JoinColumn(name="id_role_b2b", referencedColumnName="id_role", nullable=false)
     */
    private B2bRole $b2bRole;

    /**
     * @ORM\Column(name="is_default", type="boolean", options={"default"=false})
     */
    private bool $isDefault = false;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusinessEntity(): BusinessEntity
    {
        return $this->businessEntity;
    }

    public function setBusinessEntity(BusinessEntity $businessEntity): self
    {
        $this->businessEntity = $businessEntity;

        return $this;
    }

    public function getCustomerB2b(): CustomerB2b
    {
        return $this->customerB2b;
    }

    public function setCustomerB2b(CustomerB2b $customerB2b): self
    {
        $this->customerB2b = $customerB2b;

        return $this;
    }

    public function getB2bRole(): B2bRole
    {
        return $this->b2bRole;
    }

    public function setB2bRole(B2bRole $b2bRole): self
    {
        $this->b2bRole = $b2bRole;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setTimestampsOnCreate(): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
    }
}
