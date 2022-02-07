<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopUrl
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ShopUrl
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_shop_url", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Shop", inversedBy="shopUrls")
     * @ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", nullable=false)
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=150)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="domain_ssl", type="string", length=150)
     */
    private $domainSsl;

    /**
     * @var string
     *
     * @ORM\Column(name="physical_uri", type="string", length=64)
     */
    private $physicalUri;

    /**
     * @var string
     *
     * @ORM\Column(name="virtual_uri", type="string", length=64)
     */
    private $virtualUri;

    /**
     * @var bool
     *
     * @ORM\Column(name="main", type="boolean")
     */
    private $main;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domainSsl
     *
     * @return $this
     */
    public function setDomainSsl(string $domainSsl): self
    {
        $this->domainSsl = $domainSsl;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainSsl(): string
    {
        return $this->domainSsl;
    }

    /**
     * @param string $physicalUri
     *
     * @return $this
     */
    public function setPhysicalUri(string $physicalUri): self
    {
        $this->physicalUri = $physicalUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhysicalUri(): string
    {
        return $this->physicalUri;
    }

    /**
     * @param string $virtualUri
     *
     * @return $this
     */
    public function setVirtualUri(string $virtualUri): self
    {
        $this->virtualUri = $virtualUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getVirtualUri(): string
    {
        return $this->virtualUri;
    }

    /**
     * @param bool $main
     *
     * @return $this
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }

    /**
     * @return bool
     */
    public function getMain(): bool
    {
        return $this->main;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Get shopGroup.
     *
     * @return Shop
     */
    public function getShop(): Shop
    {
        return $this->shop;
    }
}
