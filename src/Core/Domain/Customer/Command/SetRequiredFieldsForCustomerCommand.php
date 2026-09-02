<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        if (!empty(array_diff($requiredFields, RequiredField::ALLOWED_REQUIRED_FIELDS))) {
            throw new InvalidCustomerRequiredFieldsException(sprintf('Invalid customer required fields provided. Allowed fields are: %s', implode(',', RequiredField::ALLOWED_REQUIRED_FIELDS)));
        }
    }
}
