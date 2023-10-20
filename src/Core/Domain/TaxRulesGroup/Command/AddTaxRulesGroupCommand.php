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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;

/**
 * Command responsible for multiple tax rules groups deletion
 */
class AddTaxRulesGroupCommand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int[]
     */
    protected $shopAssociation = [];

    /**
     * @param string $name
     * @param bool $enabled
     */
    public function __construct(string $name, bool $enabled)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function setShopAssociation(array $shopAssociation): self
    {
        if (!$this->assertArrayContainsOnlyIntegerValues($shopAssociation)) {
            throw new TaxRulesGroupConstraintException(
                sprintf(
                    'Given shop association %s must contain only integer values',
                    var_export($shopAssociation, true)
                ),
                TaxRulesGroupConstraintException::INVALID_SHOP_ASSOCIATION
            );
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertArrayContainsOnlyIntegerValues(array $values): bool
    {
        $filterAllIntegers = function ($value) {
            return is_int($value);
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterAllIntegers));
    }
}
