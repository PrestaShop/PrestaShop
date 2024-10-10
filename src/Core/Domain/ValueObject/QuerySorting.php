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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;

/**
 * Class QuerySorting is responsible for providing valid sorting parameter.
 */
class QuerySorting
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';
    public const AVAILABLE_SORTING_FIELDS = ['id_product', 'date_add', 'product_reference', 'product_supplier_reference'];
    public const AVAILABLE_SORTING_ORDER = [self::ASC, self::DESC];

    /**
     * @var array
     */
    private $sortingFields;

    /**
     * @var string
     */
    private $sortingOrder;

    /**
     * @param array $sortingFields
     * @param string $sortingOrder
     *
     * @throws InvalidSortingException
     */
    public function __construct(array $sortingFields, string $sortingOrder)
    {
        $this->assertSortingFieldsSupported($sortingFields);

        $sortingOrder = strtoupper($sortingOrder);
        $this->assertSortingOrderSupported($sortingOrder);

        $this->sortingFields = $sortingFields;
        $this->sortingOrder = $sortingOrder;
    }

    /**
     * @return array
     */
    public function getSortingFields(): array
    {
        return $this->sortingFields;
    }

    /**
     * @return string
     */
    public function getSortingOrder(): string
    {
        return $this->sortingOrder;
    }

    /**
     * @param string $sortingOrder
     *
     * @throws InvalidSortingException
     */
    private function assertSortingOrderSupported(string $sortingOrder): void
    {
        if (!in_array($sortingOrder, self::AVAILABLE_SORTING_ORDER, true)) {
            throw new InvalidSortingException(sprintf('Invalid sorting order parameter `%s`. You must use one of these options: %s', $sortingOrder, implode(', ', self::AVAILABLE_SORTING_ORDER)));
        }
    }

    /**
     * @param array $sortingFields
     *
     * @throws InvalidSortingException
     */
    private function assertSortingFieldsSupported(array $sortingFields): void
    {
        foreach ($sortingFields as $sortingField) {
            if (!in_array($sortingField, self::AVAILABLE_SORTING_FIELDS, true)) {
                throw new InvalidSortingException(sprintf('Invalid sorting field parameter `%s`. You must use one of these options: %s', $sortingField, implode(', ', self::AVAILABLE_SORTING_ORDER)));
            }
        }
    }
}
