<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class GeneralInformation.
 */
class GeneralInformation
{
    /**
     * @var string
     */
    private $privateNote;

    /**
     * @var bool
     */
    private $customerBySameEmailExists;

    /**
     * @param string $privateNote
     * @param bool $customerBySameEmailExists
     */
    public function __construct($privateNote, $customerBySameEmailExists)
    {
        $this->privateNote = $privateNote;
        $this->customerBySameEmailExists = $customerBySameEmailExists;
    }

    /**
     * @return string
     */
    public function getPrivateNote()
    {
        return $this->privateNote;
    }

    /**
     * @return bool
     */
    public function getCustomerBySameEmailExists()
    {
        return $this->customerBySameEmailExists;
    }
}
