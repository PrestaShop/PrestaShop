<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Command;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

/**
 * Partially updates a grid view: only the fields set on the command are applied.
 */
class EditGridViewCommand extends AbstractGridViewCommand
{
    private readonly GridViewId $gridViewId;

    private ?string $name = null;

    private ?bool $shared = null;

    private ?array $dynamicDateRules = null;

    /**
     * @param int $gridViewId
     *
     * @throws GridViewConstraintException
     */
    public function __construct(int $gridViewId)
    {
        $this->gridViewId = new GridViewId($gridViewId);
    }

    /**
     * @return GridViewId
     */
    public function getGridViewId(): GridViewId
    {
        return $this->gridViewId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     *
     * @throws GridViewConstraintException
     */
    public function setName(string $name): self
    {
        $this->name = $this->assertValidName($name);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isShared(): ?bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     *
     * @return self
     */
    public function setShared(bool $shared): self
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getDynamicDateRules(): ?array
    {
        return $this->dynamicDateRules;
    }

    /**
     * @param array $dynamicDateRules
     *
     * @return self
     */
    public function setDynamicDateRules(array $dynamicDateRules): self
    {
        $this->dynamicDateRules = $dynamicDateRules;

        return $this;
    }
}
