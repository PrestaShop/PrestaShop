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
 * TabLang.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TabLangRepository")
 */
class TabLang
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Tab", inversedBy="tabLangs")
     *
     * @ORM\JoinColumn(name="id_tab", referencedColumnName="id_tab", nullable=false)
     */
    private Tab $tab;

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

    public function getTab(): Tab
    {
        return $this->tab;
    }

    public function setTab(Tab $tab): static
    {
        $this->tab = $tab;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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
