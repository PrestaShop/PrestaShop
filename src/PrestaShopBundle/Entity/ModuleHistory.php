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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleHistory.
 *
 * @ORM\Table
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks
 */
class ModuleHistory
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_employee", type="integer")
     */
    private int $idEmployee;

    /**
     * @ORM\Column(name="id_module", type="integer")
     */
    private int $idModule;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTime $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTime $dateUpd;

    public function getId(): int
    {
        return $this->id;
    }

    public function setIdEmployee(int $idEmployee): static
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    public function getIdEmployee(): int
    {
        return $this->idEmployee;
    }

    public function setIdModule($idModule): static
    {
        $this->idModule = $idModule;

        return $this;
    }

    public function getIdModule(): int
    {
        return $this->idModule;
    }

    public function setDateAdd(DateTime $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }

    public function setDateUpd(DateTime $dateUpd): static
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    public function getDateUpd(): DateTime
    {
        return $this->dateUpd;
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->dateUpd = new DateTime();
        if (!isset($this->dateAdd)) {
            $this->dateAdd = new DateTime();
        }
    }
}
