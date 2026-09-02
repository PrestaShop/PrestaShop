<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
