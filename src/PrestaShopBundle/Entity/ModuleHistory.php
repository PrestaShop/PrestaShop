<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
