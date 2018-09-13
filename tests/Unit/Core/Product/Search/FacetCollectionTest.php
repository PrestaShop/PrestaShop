<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\FacetCollection;

/**
 * @doc ./vendor/bin/phpunit -c tests/phpunit.xml --filter="FacetCollectionTest"
 */
class FacetCollectionTest extends TestCase
{
    /**
     * @var FacetCollection
     */
    private $facetCollection;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->facetCollection = new FacetCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->facetCollection = null;
    }

    public function testCreateANewCollection()
    {
        $this->assertInternalType('array', $this->facetCollection->getFacets());
    }

    public function testAddFacet()
    {
        $this->assertCount(0, $this->facetCollection->getFacets());
        $facet = $this->createMock(Facet::class);
        $this->assertInstanceOf(FacetCollection::class, $this->facetCollection->addFacet($facet));

        $facets = $this->facetCollection->getFacets();
        $this->assertCount(1, $facets);
        $this->assertSame($facet, $facets[0]);
    }

    public function testSetFacets()
    {
        $this->assertCount(0, $this->facetCollection->getFacets());
        $mocks = [
            $this->createMock(Facet::class),
            $this->createMock(Facet::class),
        ];

        $this->assertInstanceOf(FacetCollection::class, $this->facetCollection->setFacets($mocks));

        $facets = $this->facetCollection->getFacets();
        $this->assertCount(2, $facets);
        $this->assertSame($mocks, $facets);
    }
}
