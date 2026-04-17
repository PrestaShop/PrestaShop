<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Searchers for customers by phrases matching customer's first name, last name, email, company name and id
 */
class SearchCustomers
{
    /**
     * @var string[]
     */
    private $phrases;

    /**
     * @var ShopConstraint|null
     */
    private $shopConstraint;

    /**
     * @var bool
     */
    private $excludeGuests;

    /**
     * @param string[] $phrases
     * @param ShopConstraint|null $shopConstraint
     * @param bool $excludeGuests
     */
    public function __construct(
        array $phrases,
        ?ShopConstraint $shopConstraint = null,
        bool $excludeGuests = false
    ) {
        $this->assertPhrasesAreNotEmpty($phrases);
        $this->assertShopConstraintIsSupported($shopConstraint);
        $this->phrases = $phrases;
        $this->shopConstraint = $shopConstraint;
        $this->excludeGuests = $excludeGuests;
    }

    /**
     * @return string[]
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
     * @return ShopConstraint|null
     */
    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return bool
     */
    public function getExcludeGuests(): bool
    {
        return $this->excludeGuests;
    }

    /**
     * @param string[] $phrases
     */
    private function assertPhrasesAreNotEmpty(array $phrases)
    {
        if (empty($phrases)) {
            throw new CustomerException('Phrases cannot be empty when searching customers.');
        }
    }

    private function assertShopConstraintIsSupported(?ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint && $shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop group constraint is not supported');
        }
    }
}
