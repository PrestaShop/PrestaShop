<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     uniqueConstraints={
 *
 *         @ORM\UniqueConstraint(name="admin_grid_configuration_idx", columns={"id_employee", "id_shop", "grid_id", "controller_route"})
 *     },
 *     indexes={
 *
 *         @ORM\Index(name="admin_grid_configuration_shop_grid_idx", columns={"id_shop", "grid_id", "controller_route"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class AdminGridConfiguration
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
    private int $employeeId;

    /**
     * @ORM\Column(name="id_shop", type="integer")
     */
    private int $shopId;

    /**
     * @ORM\Column(name="grid_id", type="string", length=191)
     */
    private string $gridId;

    /**
     * @ORM\Column(name="filter_id", type="string", length=191)
     */
    private string $filterId;

    /**
     * @ORM\Column(name="controller_route", type="string", length=255)
     */
    private string $controllerRoute;

    /**
     * @ORM\Column(name="display_shared_filters", type="boolean", options={"default": 1})
     */
    private bool $displaySharedFilters = true;

    /**
     * @ORM\Column(name="display_totals", type="boolean", options={"default": 1})
     */
    private bool $displayTotals = true;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTime $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTime $dateUpd;

    /**
     * @var Collection<int, AdminGridView>
     *
     * @ORM\OneToMany(
     *     targetEntity="PrestaShopBundle\Entity\AdminGridView",
     *     mappedBy="gridConfiguration",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private Collection $views;

    public function __construct()
    {
        $this->views = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function updateDateAdd(): void
    {
        $this->dateAdd = new DateTime();
        $this->dateUpd = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDateUpd(): void
    {
        $this->dateUpd = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     *
     * @return static
     */
    public function setEmployeeId(int $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @return static
     */
    public function setShopId(int $shopId): static
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGridId(): string
    {
        return $this->gridId;
    }

    /**
     * @param string $gridId
     *
     * @return static
     */
    public function setGridId(string $gridId): static
    {
        $this->gridId = $gridId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilterId(): string
    {
        return $this->filterId;
    }

    /**
     * @param string $filterId
     *
     * @return static
     */
    public function setFilterId(string $filterId): static
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * @return string
     */
    public function getControllerRoute(): string
    {
        return $this->controllerRoute;
    }

    /**
     * @param string $controllerRoute
     *
     * @return static
     */
    public function setControllerRoute(string $controllerRoute): static
    {
        $this->controllerRoute = $controllerRoute;

        return $this;
    }

    /**
     * @return bool
     */
    public function displaySharedFilters(): bool
    {
        return $this->displaySharedFilters;
    }

    /**
     * @param bool $displaySharedFilters
     *
     * @return static
     */
    public function setDisplaySharedFilters(bool $displaySharedFilters): static
    {
        $this->displaySharedFilters = $displaySharedFilters;

        return $this;
    }

    /**
     * @return bool
     */
    public function displayTotals(): bool
    {
        return $this->displayTotals;
    }

    /**
     * @param bool $displayTotals
     *
     * @return static
     */
    public function setDisplayTotals(bool $displayTotals): static
    {
        $this->displayTotals = $displayTotals;

        return $this;
    }

    /**
     * @return Collection<int, AdminGridView>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return static
     */
    public function addView(AdminGridView $gridView): static
    {
        if (!$this->views->contains($gridView)) {
            $this->views->add($gridView);
            $gridView->setGridConfiguration($this);
        }

        return $this;
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return static
     */
    public function removeView(AdminGridView $gridView): static
    {
        $this->views->removeElement($gridView);

        return $this;
    }
}
