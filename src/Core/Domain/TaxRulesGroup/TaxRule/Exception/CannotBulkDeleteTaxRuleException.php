<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception;

use Throwable;

/**
 * Thrown on failure to delete all selected tax rules
 */
class CannotBulkDeleteTaxRuleException extends TaxRuleException
{
    /**
     * @var int[]
     */
    private array $taxRuleIds;

    /**
     * @param int[] $taxRuleIds
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $taxRuleIds, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->taxRuleIds = $taxRuleIds;
    }

    /**
     * @return int[]
     */
    public function getTaxRuleIds(): array
    {
        return $this->taxRuleIds;
    }
}
