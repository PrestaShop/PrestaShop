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

namespace Tests\Unit\Core\Grid\Filter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterInterface;

class FilterCollectionTest extends TestCase
{
    public function testFilterCollectionIsEmptyByDefault()
    {
        $filters = new FilterCollection();

        $this->assertEmpty($filters->all());

        return $filters;
    }

    /**
     * @depends testFilterCollectionIsEmptyByDefault
     */
    public function testFilterCanBeAddedToCollection(FilterCollection $filters)
    {
        $filters->add($this->getFilterMock('first'));
        $filters->add($this->getFilterMock('second'));
        $filters->add($this->getFilterMock('third'));

        $this->assertCount(3, $filters->all());

        return $filters;
    }

    /**
     * @depends testFilterCanBeAddedToCollection
     */
    public function testFilterCanBeRemovedFromCollection(FilterCollection $filters)
    {
        $filters->remove('second');

        $this->assertCount(2, $filters->all());
    }

    private function getFilterMock($name)
    {
        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getName')
            ->willReturn($name);

        return $filter;
    }
}
