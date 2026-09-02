<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\InvalidAttributeGroupTypeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;

/**
 * Adds new attribute group
 */
class AddAttributeGroupCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @var array
     */
    private $localizedPublicNames;

    /**
     * @var AttributeGroupType
     */
    private $type;

    /**
     * @param string[] $localizedNames
     * @param array $localizedPublicNames
     * @param string $type
     * @param int[] $associatedShopIds
     *
     * @throws AttributeGroupConstraintException
     * @throws InvalidAttributeGroupTypeException
     */
    public function __construct(
        array $localizedNames,
        array $localizedPublicNames,
        string $type,
        array $associatedShopIds,
    ) {
        $this->assertNamesAreValid(
            $localizedNames,
            'Attribute name cannot be empty',
            AttributeGroupConstraintException::EMPTY_NAME
        );
        $this->assertNamesAreValid(
            $localizedPublicNames,
            'Attribute public name cannot be empty',
            AttributeGroupConstraintException::EMPTY_PUBLIC_NAME
        );

        $this->localizedNames = $localizedNames;
        $this->localizedPublicNames = $localizedPublicNames;
        $this->type = new AttributeGroupType($type);
        $this->associatedShopIds = $associatedShopIds;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedPublicNames(): array
    {
        return $this->localizedPublicNames;
    }

    /**
     * @return AttributeGroupType
     */
    public function getType(): AttributeGroupType
    {
        return $this->type;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * Asserts that attribute group's names are valid.
     *
     * @param string[] $names
     *
     * @throws AttributeGroupConstraintException
     */
    private function assertNamesAreValid(array $names, string $message, int $errorCode): void
    {
        if (empty($names)) {
            throw new AttributeGroupConstraintException($message, $errorCode);
        }
    }
}
