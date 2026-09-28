<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * A collected CSP violation report, one row per distinct (shop, directive, source); collisions bump "hits".
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\CspLogRepository")
 *
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(name="csp_log_shop_directive_source_idx", fields={"shopId", "directive", "source"})},
 *     indexes={@ORM\Index(name="csp_log_shop_prune_idx", columns={"id_shop", "hits", "date_upd"})}
 * )
 */
class CspLog
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_csp_log", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_shop", type="integer", options={"unsigned": true})
     */
    private int $shopId;

    /**
     * @ORM\Column(name="directive", type="string", length=64)
     */
    private string $directive;

    /**
     * @ORM\Column(name="source", type="string", length=255)
     */
    private string $source;

    /**
     * @ORM\Column(name="document_uri", type="string", length=2048, nullable=true)
     */
    private ?string $documentUri = null;

    /**
     * @ORM\Column(name="hits", type="integer", options={"unsigned": true, "default": 1})
     */
    private int $hits = 1;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTimeInterface $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTimeInterface $dateUpd;

    public function getId(): int
    {
        return $this->id;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getDirective(): string
    {
        return $this->directive;
    }

    public function setDirective(string $directive): self
    {
        $this->directive = $directive;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getDocumentUri(): ?string
    {
        return $this->documentUri;
    }

    public function setDocumentUri(?string $documentUri): self
    {
        $this->documentUri = $documentUri;

        return $this;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function setHits(int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateUpd(): DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }
}
