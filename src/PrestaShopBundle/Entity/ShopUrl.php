<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopUrl
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="id_shop", columns={"id_shop", "main"})},
 *     uniqueConstraints={
 *
 *         @ORM\UniqueConstraint(name="full_shop_url", columns={"domain", "physical_uri", "virtual_uri"}),
 *         @ORM\UniqueConstraint(name="full_shop_url_ssl", columns={"domain_ssl", "physical_uri", "virtual_uri"}),
 *     }
 * )
 *
 * @ORM\Entity
 */
class ShopUrl
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_shop_url", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Shop", inversedBy="shopUrls")
     *
     * @ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", nullable=false, options={"unsigned": true})
     */
    private Shop $shop;

    /**
     * @ORM\Column(name="domain", type="string", length=150)
     */
    private string $domain;

    /**
     * @ORM\Column(name="domain_ssl", type="string", length=150)
     */
    private string $domainSsl;

    /**
     * @ORM\Column(name="physical_uri", type="string", length=64)
     */
    private string $physicalUri;

    /**
     * @ORM\Column(name="virtual_uri", type="string", length=64)
     */
    private string $virtualUri;

    /**
     * @ORM\Column(name="main", type="boolean")
     */
    private bool $main;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    public function getId(): int
    {
        return $this->id;
    }

    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomainSsl(string $domainSsl): static
    {
        $this->domainSsl = $domainSsl;

        return $this;
    }

    public function getDomainSsl(): string
    {
        return $this->domainSsl;
    }

    public function setPhysicalUri(string $physicalUri): static
    {
        $this->physicalUri = $physicalUri;

        return $this;
    }

    public function getPhysicalUri(): string
    {
        return $this->physicalUri;
    }

    public function setVirtualUri(string $virtualUri): static
    {
        $this->virtualUri = $virtualUri;

        return $this;
    }

    public function getVirtualUri(): string
    {
        return $this->virtualUri;
    }

    public function setMain(bool $main): static
    {
        $this->main = $main;

        return $this;
    }

    public function getMain(): bool
    {
        return $this->main;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getShop(): Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }
}
