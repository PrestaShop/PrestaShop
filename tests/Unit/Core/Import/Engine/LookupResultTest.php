<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\EntityLookupResult;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver\ResolvedEntity;

/**
 * The finder/resolver DTOs are pure data carriers; these tests pin the few
 * derived behaviors the callers rely on for their severity decisions.
 */
class LookupResultTest extends TestCase
{
    public function testEntityLookupResultDerivesFirstCountAndAmbiguity(): void
    {
        $miss = new EntityLookupResult([]);
        $this->assertNull($miss->first());
        $this->assertNull($miss->firstMatchedBy());
        $this->assertSame(0, $miss->count());
        $this->assertFalse($miss->isAmbiguous());
        $this->assertNull($miss->forcedId);
        $this->assertFalse($miss->foundOutsideShopScope);

        $single = new EntityLookupResult([['id' => 7, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID]]);
        $this->assertSame(7, $single->first());
        $this->assertSame(EntityLookupResult::MATCHED_BY_ID, $single->firstMatchedBy());
        $this->assertFalse($single->isAmbiguous());

        $ambiguous = new EntityLookupResult([
            ['id' => 3, 'matchedBy' => EntityLookupResult::MATCHED_BY_NAME],
            ['id' => 9, 'matchedBy' => EntityLookupResult::MATCHED_BY_NAME],
        ]);
        $this->assertSame(3, $ambiguous->first(), 'The lowest id must come first');
        $this->assertSame(2, $ambiguous->count());
        $this->assertTrue($ambiguous->isAmbiguous());
    }

    /**
     * The id/reference collision of an accessory target is encoded by mixing
     * strategies: the winning id match comes first, the reference matches
     * follow — even when they designate the same entity (no deduplication
     * across strategies, that coincidence is exactly what callers warn about).
     */
    public function testMixedStrategiesEncodeTheIdReferenceCollision(): void
    {
        $collision = new EntityLookupResult([
            ['id' => 9001, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID],
            ['id' => 9001, 'matchedBy' => EntityLookupResult::MATCHED_BY_REFERENCE],
        ]);

        $this->assertSame(9001, $collision->first(), 'The id match must win');
        $this->assertSame(EntityLookupResult::MATCHED_BY_ID, $collision->firstMatchedBy());
        $this->assertTrue($collision->isAmbiguous());
    }

    public function testResolvedEntityReportsAmbiguity(): void
    {
        $this->assertFalse((new ResolvedEntity(5, true))->isAmbiguous());
        $this->assertTrue((new ResolvedEntity(5, false, 2))->isAmbiguous());
    }
}
