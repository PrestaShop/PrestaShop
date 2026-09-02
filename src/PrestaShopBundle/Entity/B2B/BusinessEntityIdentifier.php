<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessEntityIdentifier.
 *
 * @ORM\Table(indexes={
 *
 *     @ORM\Index(name="business_entity_identifier_id_business_entity_idx", columns={"id_business_entity"}),
 *     @ORM\Index(name="business_entity_identifier_id_business_identifier_idx", columns={"id_business_identifier"}),
 *     @ORM\Index(name="business_entity_identifier_value_idx", columns={"value"})
 * }, uniqueConstraints={
 *
 *     @ORM\UniqueConstraint(name="uniq_business_entity_identifier", columns={"id_business_entity", "id_business_identifier"})
 * })
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity()
 */
class BusinessEntityIdentifier
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_identifier", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntity", inversedBy="businessEntityIdentifiers")
     *
     * @ORM\JoinColumn(name="id_business_entity", referencedColumnName="id_business_entity", nullable=false)
     */
    private BusinessEntity $businessEntity;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\BusinessIdentifier", inversedBy="businessEntityIdentifiers")
     *
     * @ORM\JoinColumn(name="id_business_identifier", referencedColumnName="id_business_identifier", nullable=false)
     */
    private BusinessIdentifier $businessIdentifier;

    /**
     * @ORM\Column(name="value", type="string", length=255)
     */
    private string $value;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBusinessEntity(): BusinessEntity
    {
        return $this->businessEntity;
    }

    public function getBusinessIdentifier(): BusinessIdentifier
    {
        return $this->businessIdentifier;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setBusinessEntity(BusinessEntity $businessEntity): self
    {
        $this->businessEntity = $businessEntity;

        return $this;
    }

    public function setBusinessIdentifier(BusinessIdentifier $businessIdentifier): self
    {
        $this->businessIdentifier = $businessIdentifier;

        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();

        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
    }
}
