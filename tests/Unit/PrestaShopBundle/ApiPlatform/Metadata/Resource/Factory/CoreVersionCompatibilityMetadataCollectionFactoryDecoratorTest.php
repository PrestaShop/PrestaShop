<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\DisabledFeatureFlagStateChecker;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory\CoreVersionCompatibilityMetadataCollectionFactoryDecorator;
use RuntimeException;

class CoreVersionCompatibilityMetadataCollectionFactoryDecoratorTest extends TestCase
{
    private const CORE_VERSION = '9.2.0';

    private const EXPECTED_KEPT_OPERATIONS = [
        'no_version',
        'min_below',
        'min_equal',
        'max_above',
        'max_equal',
        'range_in',
    ];

    // All the operations in their declaration order, expected when the filtering is bypassed
    private const ALL_OPERATIONS = [
        'no_version',
        'min_below',
        'min_equal',
        'min_above',
        'max_above',
        'max_equal',
        'max_below',
        'range_in',
        'range_out',
    ];

    public function testIncompatibleOperationsAreFiltered(): void
    {
        $decorator = new CoreVersionCompatibilityMetadataCollectionFactoryDecorator(
            $this->buildDecoratedFactory(),
            new DisabledFeatureFlagStateChecker(),
            self::CORE_VERSION,
        );

        $this->assertEquals(self::EXPECTED_KEPT_OPERATIONS, $this->getOperationNames($decorator->create('resourceClass')));
    }

    public function testEnabledExperimentalEndpointsFeatureFlagFiltersNothing(): void
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')->willReturn(true);

        $decorator = new CoreVersionCompatibilityMetadataCollectionFactoryDecorator(
            $this->buildDecoratedFactory(),
            $featureFlagStateChecker,
            self::CORE_VERSION,
        );

        $this->assertEquals(self::ALL_OPERATIONS, $this->getOperationNames($decorator->create('resourceClass')));
    }

    public function testThrowingFeatureFlagCheckerStillFilters(): void
    {
        // The feature flag may not be readable during cache warmup on a not installed shop, in which case
        // the flag is considered disabled and the filtering is active
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')->willThrowException(new RuntimeException('DB is not set up'));

        $decorator = new CoreVersionCompatibilityMetadataCollectionFactoryDecorator(
            $this->buildDecoratedFactory(),
            $featureFlagStateChecker,
            self::CORE_VERSION,
        );

        $this->assertEquals(self::EXPECTED_KEPT_OPERATIONS, $this->getOperationNames($decorator->create('resourceClass')));
    }

    public function testMalformedVersionStringBehavesLikeVersionCompare(): void
    {
        // Version strings are not validated, version_compare semantics apply as-is: an unknown
        // string part is considered lower than any numeric part, so such a minVersion never filters
        // while such a maxVersion always does
        $collection = new ResourceMetadataCollection('resourceClass', [
            (new ApiResource())->withOperations(new Operations([
                'malformed_min' => new CQRSGet(uriTemplate: '/malformed/min', minVersion: 'not-a-version'),
                'malformed_max' => new CQRSGet(uriTemplate: '/malformed/max', maxVersion: 'not-a-version'),
            ])),
        ]);
        $decoratedFactory = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $decoratedFactory->method('create')->willReturn($collection);

        $decorator = new CoreVersionCompatibilityMetadataCollectionFactoryDecorator(
            $decoratedFactory,
            new DisabledFeatureFlagStateChecker(),
            self::CORE_VERSION,
        );

        $this->assertEquals(['malformed_min'], $this->getOperationNames($decorator->create('resourceClass')));
    }

    private function buildDecoratedFactory(): ResourceMetadataCollectionFactoryInterface
    {
        $collection = new ResourceMetadataCollection('resourceClass', [
            (new ApiResource())->withOperations(new Operations([
                'no_version' => new CQRSGet(uriTemplate: '/no-version'),
                'min_below' => new CQRSGet(uriTemplate: '/min-below', minVersion: '1.0.0'),
                'min_equal' => new CQRSGet(uriTemplate: '/min-equal', minVersion: self::CORE_VERSION),
                'min_above' => new CQRSGet(uriTemplate: '/min-above', minVersion: '99.99.99'),
                'max_above' => new CQRSGet(uriTemplate: '/max-above', maxVersion: '99.99.99'),
                'max_equal' => new CQRSGet(uriTemplate: '/max-equal', maxVersion: self::CORE_VERSION),
                'max_below' => new CQRSGet(uriTemplate: '/max-below', maxVersion: '1.0.0'),
                'range_in' => new CQRSGet(uriTemplate: '/range-in', minVersion: '1.0.0', maxVersion: '99.99.99'),
                'range_out' => new CQRSGet(uriTemplate: '/range-out', minVersion: '98.0.0', maxVersion: '99.99.99'),
            ])),
        ]);

        $decoratedFactory = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $decoratedFactory->method('create')->willReturn($collection);

        return $decoratedFactory;
    }

    /**
     * @return string[]
     */
    private function getOperationNames(ResourceMetadataCollection $resourceMetadataCollection): array
    {
        $operationNames = [];
        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            foreach ($resourceMetadata->getOperations() as $operationName => $operation) {
                $operationNames[] = $operationName;
            }
        }

        return $operationNames;
    }
}
