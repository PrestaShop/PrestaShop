<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;

class CombinationWebserviceTest extends TestCase
{
    use ContextMockerTrait;

    private const ID_PRODUCT_ATTRIBUTE = 999002;
    private const OTHER_ID_PRODUCT_ATTRIBUTE = 999003;

    /**
     * @var Combination
     */
    private $combination;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->combination = new Combination();
        $this->combination->id = self::ID_PRODUCT_ATTRIBUTE;
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete(
            'feature_product_attribute',
            '`id_product_attribute` IN (' . self::ID_PRODUCT_ATTRIBUTE . ', ' . self::OTHER_ID_PRODUCT_ATTRIBUTE . ')'
        );
        parent::tearDown();
    }

    public function testCombinationFeaturesAssociationIsExposed(): void
    {
        $associations = $this->combination->getWebserviceParameters()['associations'];

        $this->assertArrayHasKey('combination_features', $associations);
        $this->assertSame('combination_feature', $associations['combination_features']['resource']);
    }

    public function testSetAndGetCombinationFeatures(): void
    {
        $this->assertTrue($this->combination->setWsCombinationFeatures([
            ['id' => 2, 'id_feature_value' => 21],
            ['id' => 1, 'id_feature_value' => 11],
            ['id' => 1, 'id_feature_value' => 12],
        ]));

        $this->assertEquals([
            ['id' => 1, 'id_feature_value' => 11],
            ['id' => 1, 'id_feature_value' => 12],
            ['id' => 2, 'id_feature_value' => 21],
        ], $this->combination->getWsCombinationFeatures());
    }

    public function testSetCombinationFeaturesReplacesPreviousOnesOfThisCombinationOnly(): void
    {
        $otherCombination = new Combination();
        $otherCombination->id = self::OTHER_ID_PRODUCT_ATTRIBUTE;
        $otherCombination->setWsCombinationFeatures([['id' => 1, 'id_feature_value' => 11]]);

        $this->combination->setWsCombinationFeatures([['id' => 1, 'id_feature_value' => 11]]);
        $this->combination->setWsCombinationFeatures([['id' => 3, 'id_feature_value' => 31]]);

        $this->assertEquals([['id' => 3, 'id_feature_value' => 31]], $this->combination->getWsCombinationFeatures());
        $this->assertEquals([['id' => 1, 'id_feature_value' => 11]], $otherCombination->getWsCombinationFeatures());
    }

    public function testSetEmptyCombinationFeaturesRemovesAll(): void
    {
        $this->combination->setWsCombinationFeatures([['id' => 1, 'id_feature_value' => 11]]);

        $this->assertTrue($this->combination->setWsCombinationFeatures([]));
        $this->assertSame([], $this->combination->getWsCombinationFeatures());
    }
}
