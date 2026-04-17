<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Carries data for customer thread view
 */
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
     * @var CustomerThreadMessage[]
     */
    private $messages;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var CustomerThreadTimeline
     */
    private $timeline;

    /**
     * @param CustomerThreadId $customerThreadId
     * @param LanguageId $languageId
     * @param array $actions
     * @param CustomerInformation $customerInformation
     * @param string $contactName
     * @param CustomerThreadMessage[] $messages
     * @param CustomerThreadTimeline $timeline
     */
    public function __construct(
        CustomerThreadId $customerThreadId,
        LanguageId $languageId,
        array $actions,
        CustomerInformation $customerInformation,
        $contactName,
        array $messages,
        CustomerThreadTimeline $timeline
    ) {
        $this->customerThreadId = $customerThreadId;
        $this->actions = $actions;
        $this->customerInformation = $customerInformation;
        $this->contactName = $contactName;
        $this->messages = $messages;
        $this->languageId = $languageId;
        $this->timeline = $timeline;
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
     * @return CustomerThreadMessage[]
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

    /**
     * @return CustomerThreadTimeline
     */
    public function getTimeline()
    {
        return $this->timeline;
    }
}
