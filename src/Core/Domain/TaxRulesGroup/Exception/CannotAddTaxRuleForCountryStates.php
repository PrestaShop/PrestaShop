<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception;

use Exception;

/**
 * Thrown on failure to add tax rule for certain country states
 */
class CannotAddTaxRuleForCountryStates extends TaxRulesGroupException
{
    /**
     * Id of states country that reported an error when tax rule was added
     *
     * @var int
     */
    private $failedRuleCountryId;

    /**
     * List of state that reported an error when tax rule was added
     *
     * @var array
     */
    private $failedRuleStatesIds;

    /**
     * @param int $failedRuleCountryId
     * @param array $failedRuleStatesIds
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(int $failedRuleCountryId, array $failedRuleStatesIds, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->failedRuleCountryId = $failedRuleCountryId;
        $this->failedRuleStatesIds = $failedRuleStatesIds;
    }

    /**
     * @return int
     */
    public function getFailedRuleCountryId(): int
    {
        return $this->failedRuleCountryId;
    }

    /**
     * @return array
     */
    public function getFailedRuleStatesIds(): array
    {
        return $this->failedRuleStatesIds;
    }
}
