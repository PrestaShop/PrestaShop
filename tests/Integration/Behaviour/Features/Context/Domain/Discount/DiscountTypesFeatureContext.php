<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain\Discount;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountTypes;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

class DiscountTypesFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then I should receive the following discount types:
     *
     * @param TableNode $table
     */
    public function assertDiscountTypes(TableNode $table): void
    {
        $queryBus = $this->getQueryBus();
        $query = new GetDiscountTypes();
        $discountTypes = $queryBus->handle($query);

        Assert::assertNotNull($discountTypes, 'No discount types received');
        Assert::assertIsArray($discountTypes, 'Discount types should be an array');

        $expectedDiscountTypes = $this->localizeByColumns($table);

        Assert::assertGreaterThanOrEqual(
            count($expectedDiscountTypes),
            count($discountTypes),
            sprintf('Expected %d discount types but got %d', count($expectedDiscountTypes), count($discountTypes))
        );

        foreach ($expectedDiscountTypes as $expectedDiscountType) {
            $foundDiscountType = null;
            foreach ($discountTypes as $discountType) {
                if ($discountType->getType() === $expectedDiscountType['type']) {
                    $foundDiscountType = $discountType;
                    break;
                }
            }

            if (null === $foundDiscountType) {
                throw new RuntimeException(sprintf('Couldnt find discount type "%s"', $expectedDiscountType['type']));
            }

            if (isset($expectedDiscountType['discountTypeId'])) {
                Assert::assertEquals(
                    (int) $expectedDiscountType['discountTypeId'],
                    $foundDiscountType->getDiscountTypeId(),
                    sprintf('Unexpected discountTypeId for type "%s"', $expectedDiscountType['type'])
                );
            }

            if (isset($expectedDiscountType['core'])) {
                $expectedCore = filter_var($expectedDiscountType['core'], FILTER_VALIDATE_BOOLEAN);
                Assert::assertEquals(
                    $expectedCore,
                    $foundDiscountType->isCore(),
                    sprintf('Unexpected core value for type "%s"', $expectedDiscountType['type'])
                );
            }

            if (isset($expectedDiscountType['enabled'])) {
                $expectedEnabled = filter_var($expectedDiscountType['enabled'], FILTER_VALIDATE_BOOLEAN);
                Assert::assertEquals(
                    $expectedEnabled,
                    $foundDiscountType->isEnabled(),
                    sprintf('Unexpected enabled value for type "%s"', $expectedDiscountType['type'])
                );
            }

            if (isset($expectedDiscountType['names'])) {
                $actualNames = $foundDiscountType->getLocalizedNames();
                foreach ($expectedDiscountType['names'] as $langId => $expectedName) {
                    Assert::assertEquals(
                        $expectedName,
                        $actualNames[$langId],
                        sprintf('Unexpected name for language ID %d in type "%s"', $langId, $expectedDiscountType['type'])
                    );
                }
            }

            if (isset($expectedDiscountType['descriptions'])) {
                $actualDescriptions = $foundDiscountType->getLocalizedDescriptions();
                foreach ($expectedDiscountType['descriptions'] as $langId => $expectedDescription) {
                    Assert::assertEquals(
                        $expectedDescription,
                        $actualDescriptions[$langId],
                        sprintf('Unexpected description for language ID %d in type "%s"', $langId, $expectedDiscountType['type'])
                    );
                }
            }
        }
    }
}
