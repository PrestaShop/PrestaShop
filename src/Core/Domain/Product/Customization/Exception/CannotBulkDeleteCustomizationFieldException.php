<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use Throwable;

/**
 * Thrown when customization field deletion fails in bulk action
 */
class CannotBulkDeleteCustomizationFieldException extends ProductException
{
    /**
     * @var int[]
     */
    private $customizationFieldIds;

    /**
     * @param array $customizationFieldIds
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $customizationFieldIds, $message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->customizationFieldIds = $customizationFieldIds;
    }

    /**
     * @return int[]
     */
    public function getCustomizationFieldIds(): array
    {
        return $this->customizationFieldIds;
    }
}
