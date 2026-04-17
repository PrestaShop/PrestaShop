<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address\Repository;

use Address;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Provides access to address data source
 */
class AddressRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @param AddressId $addressId
     *
     * @return Address
     *
     * @throws AttributeNotFoundException
     * @throws CoreException
     */
    public function get(AddressId $addressId): Address
    {
        /** @var Address $address */
        $address = $this->getObjectModel(
            $addressId->getValue(),
            Address::class,
            AddressNotFoundException::class
        );

        return $address;
    }
}
