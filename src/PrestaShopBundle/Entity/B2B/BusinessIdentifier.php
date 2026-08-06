<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessIdentifier.
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="business_identifier_zone_idx", columns={"id_zone"})
 *     }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity()
 */
class BusinessIdentifier
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_business_identifier", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="unremovable", type="boolean", options={"default"=false})
     */
    private bool $unremovable = false;

    /**
     * @ORM\Column(name="id_zone", type="integer", nullable=true, options={"unsigned"=true})
     */
    private ?int $idZone = null;

    /**
     * @ORM\Column(name="deleted", type="boolean", options={"default"=false})
     */
    private bool $deleted = false;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     */
    private string $label;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntityIdentifier", mappedBy="businessIdentifier")
     */
    private Collection $businessEntityIdentifiers;

    public function __construct()
    {
        $this->businessEntityIdentifiers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUnremovable(): bool
    {
        return $this->unremovable;
    }

    public function setUnremovable(bool $unremovable): self
    {
        $this->unremovable = $unremovable;

        return $this;
    }

    public function getIdZone(): ?int
    {
        return $this->idZone;
    }

    public function setIdZone(?int $idZone): self
    {
        $this->idZone = $idZone;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getBusinessEntityIdentifiers(): Collection
    {
        return $this->businessEntityIdentifiers;
    }

    public function addBusinessEntityIdentifier(BusinessEntityIdentifier $businessEntityIdentifier): self
    {
        if (!$this->businessEntityIdentifiers->contains($businessEntityIdentifier)) {
            $this->businessEntityIdentifiers[] = $businessEntityIdentifier;
            $businessEntityIdentifier->setBusinessIdentifier($this);
        }

        return $this;
    }

    public function removeBusinessEntityIdentifier(BusinessEntityIdentifier $businessEntityIdentifier): self
    {
        $this->businessEntityIdentifiers->removeElement($businessEntityIdentifier);

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
