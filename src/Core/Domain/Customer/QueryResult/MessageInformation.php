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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerMessageInformation holds customer message information.
 */
class MessageInformation
{
    /**
     * @var int
     */
    private $customerThreadId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $date;

    /**
     * @param int $customerThreadId
     * @param string $message
     * @param string $status
     * @param string $date
     */
    public function __construct($customerThreadId, $message, $status, $date)
    {
        $this->customerThreadId = $customerThreadId;
        $this->message = $message;
        $this->status = $status;
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
}
