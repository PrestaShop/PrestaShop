<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\RequiredFields;

/**
 * Sets required fields for new address when adding
 */
class SetRequiredFieldsForAddressCommand
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
        foreach ($requiredFields as $requiredField) {
            if (!in_array($requiredField, RequiredFields::ALLOWED_REQUIRED_FIELDS)) {
                throw new AddressConstraintException(sprintf('Required field %s is invalid. Allowed fields are: %s', $requiredField, implode(',', RequiredFields::ALLOWED_REQUIRED_FIELDS)), AddressConstraintException::INVALID_REQUIRED_FIELDS);
            }
        }
    }
}
