<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\InvalidCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\RequiredField;

/**
 * Sets required fields for new customer when signing up in Front Office
 */
class SetRequiredFieldsForCustomerCommand
{
    /**
     * @var string[]
     */
    private $requiredFields;

    /**
     * @param string[] $requiredFields
     */
    public function __construct(array $requiredFields)
    {
        $this->assertContainsOnlyAllowedFields($requiredFields);

        $this->requiredFields = $requiredFields;
    }

    /**
     * @return string[]
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    /**
     * Check that all provided fields are allowed.
     *
     * @param string[] $requiredFields
     */
    private function assertContainsOnlyAllowedFields(array $requiredFields)
    {
        if (empty($requiredFields)) {
            return;
        }

        $allowedRequiredFields = [
            RequiredField::PARTNER_OFFERS,
        ];

        if (!empty(array_diff($requiredFields, $allowedRequiredFields))) {
            throw new InvalidCustomerRequiredFieldsException(
                sprintf(
                    'Invalid customer required fields provided. Allowed fields are: %s',
                    implode(',', $allowedRequiredFields)
                )
            );
        }
    }
}
