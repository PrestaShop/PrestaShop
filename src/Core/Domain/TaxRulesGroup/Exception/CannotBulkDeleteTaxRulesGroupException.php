<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception;

use Exception;

/**
 * Thrown on failure to delete all selected tax rules groups without errors
 */
class CannotBulkDeleteTaxRulesGroupException extends TaxRulesGroupException
{
    /**
     * @var int[]
     */
    private $taxRulesGroupsIds;

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
