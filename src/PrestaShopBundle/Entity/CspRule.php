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
 * A curated CSP allow-list entry for a shop: one (directive, source) the merchant has approved.
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\CspRuleRepository")
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="csp_rule_shop_directive_source_idx", fields={"shopId", "context", "directive", "source"})})
 */
class CspRule
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_csp_rule", type="integer", options={"unsigned": true})
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
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTimeInterface $dateAdd;

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

    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }
}
