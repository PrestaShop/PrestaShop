<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tab\Command;

class UpdateTabStatusByClassNameCommand
{
    /**
     * @var bool
     */
    private $status;

    /**
     * @var string
     */
    private $className;

    public function __construct(string $className, bool $status)
    {
        $this->className = $className;
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
