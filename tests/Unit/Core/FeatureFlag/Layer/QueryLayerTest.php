<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\FeatureFlag\Layer;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Environment;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\QueryLayer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryLayerTest extends TestCase
{
    private const FEATURE_FLAG_TEST = 'feature_flag_test';
    private const VAR_FEATURE_FLAG_TEST = 'PS_FF_FEATURE_FLAG_TEST';

    public function provideEnabledValues(): Generator
    {
        yield ['1'];
        yield ['true'];
        yield ['TRUE'];
        yield ['on'];
        yield ['yes'];
    }

    public function provideDisabledValues(): Generator
    {
        yield ['0'];
        yield ['false'];
        yield ['FALSE'];
        yield ['off'];
        yield ['no'];
    }

    public function testIsReadonly()
    {
        $layer = $this->createLayer(true);
        $this->assertTrue($layer->isReadonly());
    }

    public function testGetTypeName()
    {
        $layer = $this->createLayer(true);
        $this->assertEquals('query', $layer->getTypeName());
    }

    public function testGetConstName()
    {
        $layer = $this->createLayer(true);
        $this->assertEquals(
            self::VAR_FEATURE_FLAG_TEST,
            $layer->getVarName(self::FEATURE_FLAG_TEST)
        );
    }

    public function testCanBeUsed()
    {
        $layer = $this->createLayer(true, 'on');
        $this->assertTrue($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    public function testCannotBeUsedWhenStateNotAvailable()
    {
        $layer = $this->createLayer(true);
        $this->assertFalse($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    public function testCannotBeUsedInProdEvenStateAvailable()
    {
        $layer = $this->createLayer(false, 'on');
        $this->assertFalse($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideEnabledValues
     */
    public function testIsEnabled(string $enabledValue)
    {
        $layer = $this->createLayer(true, $enabledValue);
        $this->assertTrue($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideDisabledValues
     */
    public function testIsDisabled(string $disabledValue)
    {
        $layer = $this->createLayer(true, $disabledValue);
        $this->assertFalse($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    public function testEnable()
    {
        $layer = $this->createLayer(true);
        $this->expectException(InvalidArgumentException::class);
        $layer->enable(self::FEATURE_FLAG_TEST);
    }

    public function testDisable()
    {
        $layer = $this->createLayer(true);
        $this->expectException(InvalidArgumentException::class);
        $layer->disable(self::FEATURE_FLAG_TEST);
    }

    private function createLayer(bool $modeDev, ?string $featureFlagState = null): QueryLayer
    {
        return new QueryLayer(
            $this->createEnvironment($modeDev),
            $this->createRequestStack($featureFlagState)
        );
    }

    private function createEnvironment(bool $status): Environment
    {
        return new Environment($status, 'unit');
    }

    private function createRequestStack(?string $status): RequestStack
    {
        $requestStack = new RequestStack();
        $request = new Request();

        if (null !== $status) {
            $request->query->set(self::VAR_FEATURE_FLAG_TEST, $status);
        }

        $requestStack->push($request);

        return $requestStack;
    }
}
