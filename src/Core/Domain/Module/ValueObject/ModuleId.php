<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\ValueObject;

/**
 * Class ModuleId is responsible for providing currency id data.
 */
class ModuleId
{
    /**
     * @var int
     */
    private $moduleId;

    /**
     * @param int $moduleId
     */
    public function __construct(int $moduleId)
    {
        $this->moduleId = $moduleId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->moduleId;
    }
}
