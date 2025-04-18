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

/**
 * AttributeGroupLang.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeGroupLangRepository")
 */
class AttributeGroupLang
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\AttributeGroup", inversedBy="attributeGroupLangs")
     *
     * @ORM\JoinColumn(name="id_attribute_group", referencedColumnName="id_attribute_group", nullable=false, onDelete="CASCADE")
     */
    private AttributeGroup $attributeGroup;

    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private Lang $lang;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private string $name;

    /**
     * @ORM\Column(name="public_name", type="string", length=64)
     */
    private string $publicName;

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPublicName(string $publicName): static
    {
        $this->publicName = $publicName;

        return $this;
    }

    public function getPublicName(): string
    {
        return $this->publicName;
    }

    public function setAttributeGroup(AttributeGroup $attributeGroup): static
    {
        $this->attributeGroup = $attributeGroup;

        return $this;
    }

    public function getAttributeGroup(): AttributeGroup
    {
        return $this->attributeGroup;
    }

    public function setLang(Lang $lang): static
    {
        $this->lang = $lang;

        return $this;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }
}
