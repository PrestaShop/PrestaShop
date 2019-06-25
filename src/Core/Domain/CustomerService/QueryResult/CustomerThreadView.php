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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

class CustomerThreadView
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @var array
     */
    private $actions;

    /**
     * @var CustomerInformation
     */
    private $customerInformation;

    /**
     * @var string
     */
    private $contactName;

    /**
     * @var array
     */
    private $messages;

    /**
     * @var LanguageId
     */
    private $languageId;

    public function __construct(
        CustomerThreadId $customerThreadId,
        LanguageId $languageId,
        array $actions,
        CustomerInformation $customerInformation,
        $contactName,
        array $messages
    ) {
        $this->customerThreadId = $customerThreadId;
        $this->actions = $actions;
        $this->customerInformation = $customerInformation;
        $this->contactName = $contactName;
        $this->messages = $messages;
        $this->languageId = $languageId;
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return CustomerInformation
     */
    public function getCustomerInformation()
    {
        return $this->customerInformation;
    }

    /**
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}
