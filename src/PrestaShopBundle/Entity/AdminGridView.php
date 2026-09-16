<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="admin_grid_view_configuration_idx", columns={"id_admin_grid_configuration"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AdminGridViewRepository")
 */
class AdminGridView
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
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\AdminGridConfiguration", inversedBy="views")
     *
     * @ORM\JoinColumn(name="id_admin_grid_configuration", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private AdminGridConfiguration $gridConfiguration;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(name="filter_id", type="string", length=191)
     */
    private string $filterId;

    /**
     * @ORM\Column(name="filters", type="text")
     */
    private string $filters;

    /**
     * @var array<string, array{date_rule: string, custom_days?: int|null}>|null
     *
     * @ORM\Column(name="dynamic_date_rules", type="json", nullable=true)
     */
    private ?array $dynamicDateRules = null;

    /**
     * @ORM\Column(name="grid_state", type="json", nullable=true)
     */
    private ?array $gridState = null;

    /**
     * @ORM\Column(name="shared", type="boolean", options={"default": 0})
     */
    private bool $shared = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return AdminGridConfiguration
     */
    public function getGridConfiguration(): AdminGridConfiguration
    {
        return $this->gridConfiguration;
    }

    /**
     * @param AdminGridConfiguration $gridConfiguration
     *
     * @return static
     */
    public function setGridConfiguration(AdminGridConfiguration $gridConfiguration): static
    {
        $this->gridConfiguration = $gridConfiguration;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

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
    public function getFilters(): string
    {
        return $this->filters;
    }

    /**
     * @param string $filters
     *
     * @return static
     */
    public function setFilters(string $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return array<string, array{date_rule: string, custom_days?: int|null}>|null
     */
    public function getDynamicDateRules(): ?array
    {
        return $this->dynamicDateRules;
    }

    /**
     * @param array<string, array{date_rule: string, custom_days?: int|null}>|null $dynamicDateRules
     */
    public function setDynamicDateRules(?array $dynamicDateRules): static
    {
        $this->dynamicDateRules = $dynamicDateRules;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getGridState(): ?array
    {
        return $this->gridState;
    }

    /**
     * @param array|null $gridState
     *
     * @return static
     */
    public function setGridState(?array $gridState): static
    {
        $this->gridState = $gridState;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     *
     * @return static
     */
    public function setShared(bool $shared): static
    {
        $this->shared = $shared;

        return $this;
    }
}
