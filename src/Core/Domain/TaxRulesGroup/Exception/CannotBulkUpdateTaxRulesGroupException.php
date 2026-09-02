<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception;

use Exception;

/**
 * Thrown on failure to bulk update tax rules groups without errors
 */
class CannotBulkUpdateTaxRulesGroupException extends TaxRulesGroupException
{
    /**
     * @var int[]
     */
    private $taxRulesGroupsIds;

    /**
     * @param array $taxRulesGroupsIds
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $taxRulesGroupsIds, $message = '', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->taxRulesGroupsIds = $taxRulesGroupsIds;
    }

    /**
     * @return int[]
     */
    public function getTaxRulesGroupsIds()
    {
        return $this->taxRulesGroupsIds;
    }
}
