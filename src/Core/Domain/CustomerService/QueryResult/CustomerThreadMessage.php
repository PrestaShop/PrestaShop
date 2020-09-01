<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Carries data for single customer thread message
 */
class CustomerThreadMessage
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string|null
     */
    private $employeeImage;

    /**
     * @var string|null
     */
    private $employeeName;

    /**
     * @var string|null
     */
    private $customerName;

    /**
     * @var string|null
     */
    private $attachmentFile;

    /**
     * @var int|null
     */
    private $productId;

    /**
     * @var string|null
     */
    private $productName;

    /**
     * @param string $type
     * @param string $message
     * @param string $date
     * @param string|null $employeeImage
     * @param string|null $employeeName
     * @param string|null $customerName
     * @param string|null $attachmentFile
     * @param int|null $productId
     * @param string|null $productName
     */
    public function __construct(
        $type,
        $message,
        $date,
        $employeeImage,
        $employeeName,
        $customerName,
        $attachmentFile,
        $productId,
        $productName
    ) {
        $this->type = $type;
        $this->message = $message;
        $this->date = $date;
        $this->employeeImage = $employeeImage;
        $this->employeeName = $employeeName;
        $this->customerName = $customerName;
        $this->attachmentFile = $attachmentFile;
        $this->productId = $productId;
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getEmployeeImage()
    {
        return $this->employeeImage;
    }

    /**
     * @return string|null
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @return string|null
     */
    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string|null
     */
    public function getProductName()
    {
        return $this->productName;
    }
}
