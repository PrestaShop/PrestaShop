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

namespace PrestaShopBundle\Entity\Employee;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table
 *
 * @ORM\HasLifecycleCallbacks
 */
class EmployeeSession
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_employee_session", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Employee\Employee", inversedBy="sessions")
     *
     * @ORM\JoinColumn(name="id_employee", referencedColumnName="id_employee", nullable=true, options={"unsigned": true}, onDelete="CASCADE")
     */
    private ?Employee $employee;

    /**
     * @ORM\Column(name="token", type="string", length=40, nullable=true)
     */
    private string $token;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTime $dateAdd;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTime $dateUpd;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): EmployeeSession
    {
        $this->employee = $employee;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): EmployeeSession
    {
        $this->token = $token;

        return $this;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
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
    public function updatedTimestamps()
    {
        $this->dateUpd = new DateTime();
        if (!isset($this->dateAdd)) {
            $this->dateAdd = new DateTime();
        }
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->token = $data['token'];
    }
}
