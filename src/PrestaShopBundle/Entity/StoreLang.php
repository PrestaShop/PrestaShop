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

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints as PrestaShopAssert;
use PrestaShopBundle\Entity\Repository\StoreLangRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass=StoreLangRepository::class)
 */
class StoreLang
{
    /**
     * @var Store
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="storeLangs")
     * @ORM\JoinColumn(name="id_store", referencedColumnName="id_store", nullable=false, onDelete="CASCADE")
     */
    private $store;

    /**
     * @var Lang
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @PrestaShopAssert\TypedRegex(type="generic_name")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @PrestaShopAssert\TypedRegex(type="address")
     * @ORM\Column(type="string", length=255)
     */
    private $address1;

    /**
     * @PrestaShopAssert\TypedRegex(type="address")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @Assert\Json
     * @ORM\Column(type="text", nullable=true)
     */
    private $hours;

    /**
     * @PrestaShopAssert\CleanHtml
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    public function getStore(): Store
    {
        return $this->store;
    }

    public function setStore(Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }

    public function setLang(Lang $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(?string $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
