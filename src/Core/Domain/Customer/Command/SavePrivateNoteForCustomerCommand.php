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

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Saves private note for customer that can only be seen in Back Office
 */
class SavePrivateNoteForCustomerCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var string
     */
    private $privateNote;

    /**
     * @param int $customerId
     * @param string $privateNote
     */
    public function __construct($customerId, $privateNote)
    {
        $this->assertPrivateNoteIsString($privateNote);

        $this->customerId = new CustomerId($customerId);
        $this->privateNote = $privateNote;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getPrivateNote()
    {
        return $this->privateNote;
    }

    /**
     * @param string $privateNote
     *
     * @throws CustomerConstraintException
     */
    private function assertPrivateNoteIsString($privateNote)
    {
        if (!is_string($privateNote)) {
            throw new CustomerConstraintException(
                'Invalid private note provided. Private note must be a string.',
                CustomerConstraintException::INVALID_PRIVATE_NOTE
            );
        }
    }
}
