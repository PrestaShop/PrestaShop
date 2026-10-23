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
 * A collected CSP violation report, one row per distinct (shop, directive, source, page); collisions bump "hits".
 * Keeping the document URI in the key gives the merchant a per-page view of where each source is blocked.
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\CspLogRepository")
 *
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(name="csp_log_shop_directive_source_doc_idx", fields={"shopId", "context", "directive", "source", "documentUri"})},
 *     indexes={@ORM\Index(name="csp_log_shop_prune_idx", columns={"id_shop", "context", "hits", "date_upd"})}
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
     * @ORM\Column(name="context", type="string", length=10, options={"default": "front"})
     */
    private string $context = 'front';

    /**
     * @ORM\Column(name="directive", type="string", length=64)
     */
    private string $directive;

    /**
     * @ORM\Column(name="source", type="string", length=255)
     */
    private string $source;

    /**
     * @ORM\Column(name="document_uri", type="string", length=255, options={"default": ""})
     */
    private string $documentUri = '';

    /**
     * A short sample of the offending inline code (script/style), when the browser supplies one. Informational
     * only: not part of the unique key, refreshed to the latest report for the (shop, directive, source, page).
     *
     * @ORM\Column(name="sample", type="string", length=64, nullable=true)
     */
    private ?string $sample = null;

    /**
     * @ORM\Column(name="source_file", type="string", length=255, nullable=true)
     */
    private ?string $sourceFile = null;

    /**
     * @ORM\Column(name="line_number", type="integer", nullable=true, options={"unsigned": true})
     */
    private ?int $lineNumber = null;

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

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

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

    public function getDocumentUri(): string
    {
        return $this->documentUri;
    }

    public function setDocumentUri(string $documentUri): self
    {
        $this->documentUri = $documentUri;

        return $this;
    }

    public function getSample(): ?string
    {
        return $this->sample;
    }

    public function setSample(?string $sample): self
    {
        $this->sample = $sample;

        return $this;
    }

    public function getSourceFile(): ?string
    {
        return $this->sourceFile;
    }

    public function setSourceFile(?string $sourceFile): self
    {
        $this->sourceFile = $sourceFile;

        return $this;
    }

    public function getLineNumber(): ?int
    {
        return $this->lineNumber;
    }

    public function setLineNumber(?int $lineNumber): self
    {
        $this->lineNumber = $lineNumber;

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
