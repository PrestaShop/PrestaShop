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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tab.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TabRepository")
 */
class Tab
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_tab", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_parent", type="integer")
     */
    private $idParent;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=64, nullable=true)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=64)
     */
    private $className;

    /**
     * @var string
     *
     * @ORM\Column(name="route_name", type="string", length=256, nullable=true)
     */
    private $routeName;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=32, nullable=true)
     */
    private $icon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="wording", type="string", length=255, nullable=true)
     */
    private $wording;

    /**
     * @var string|null
     *
     * @ORM\Column(name="wording_domain", type="string", length=255, nullable=true)
     */
    private $wordingDomain;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\TabLang", mappedBy="id")
     */
    private $tabLangs;

    public function getId()
    {
        return $this->id;
    }

    public function getIdParent()
    {
        return $this->idParent;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getTabLangs(): Collection & Selectable
    {
        return $this->tabLangs;
    }

    public function getTabLangByLanguageId(int $languageId): ?TabLang
    {
        /** @var TabLang $tabLang */
        foreach ($this->getTabLangs() as $tabLang) {
            if ($tabLang->getLang()->getId() === $languageId) {
                return $tabLang;
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getWording(): ?string
    {
        return $this->wording;
    }

    /**
     * @return string|null
     */
    public function getWordingDomain(): ?string
    {
        return $this->wordingDomain;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Tab
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     *
     * @return Tab
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return Tab
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
