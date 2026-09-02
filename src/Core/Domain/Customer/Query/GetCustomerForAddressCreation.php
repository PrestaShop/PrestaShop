<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

/**
 * Gets customer information for address creation.
 */
class GetCustomerForAddressCreation
{
    /**
     * @var string
     */
    private $customerEmail;

    /**
     * Query is used for customer search so email string might not be complete so no email validation
     *
     * @param string $customerEmail
     */
    public function __construct(string $customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
}
