<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
