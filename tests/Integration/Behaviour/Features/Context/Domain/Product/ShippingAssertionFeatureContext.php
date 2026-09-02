<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ShippingAssertionFeatureContext extends AbstractShippingFeatureContext
{
    /**
     * @Then product :productReference should have no carriers assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoCarriers(string $productReference): void
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEmpty(
            $productForEditing->getShippingInformation()->getCarrierReferences(),
            sprintf('Expected product "%s" to have no carriers assigned', $productReference)
        );
    }

    /**
     * @Then product :productReference should have following shipping information:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertShippingInformationForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertShippingInfo($productReference, $tableNode);
    }

    /**
     * @Then product :productReference should have following shipping information for shop(s) :shopReferences:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     * @param string $shopReferences
     */
    public function assertShippingInfoForShops(string $productReference, TableNode $tableNode, string $shopReferences): void
    {
        $shopReferences = explode(',', $shopReferences);

        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get(trim($shopReference));
            $this->assertShippingInfo($productReference, $tableNode, $shopId);
        }
    }

    /**
     * @param array $expectedValues
     * @param ProductShippingInformation $actualValues
     */
    private function assertNumberShippingFields(array &$expectedValues, ProductShippingInformation $actualValues): void
    {
        $numberShippingFields = [
            'width',
            'height',
            'depth',
            'weight',
            'additional_shipping_cost',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberShippingFields as $field) {
            if (isset($expectedValues[$field])) {
                $expectedNumber = new DecimalNumber((string) $expectedValues[$field]);
                $actualNumber = $propertyAccessor->getValue($actualValues, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"', $field, $expectedNumber, $actualNumber)
                    );
                }

                unset($expectedValues[$field]);
            }
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $tableNode
     * @param int|null $shopId
     */
    private function assertShippingInfo(string $productReference, TableNode $tableNode, ?int $shopId = null): void
    {
        $data = $this->localizeByRows($tableNode);
        $productShippingInformation = $this->getProductForEditing(
            $productReference,
            $shopId
        )->getShippingInformation();

        if (isset($data['carriers'])) {
            $expectedReferenceIds = $this->getCarrierReferenceIds(PrimitiveUtils::castStringArrayIntoArray($data['carriers']));
            $actualReferenceIds = $productShippingInformation->getCarrierReferences();

            Assert::assertEquals(
                $expectedReferenceIds,
                $actualReferenceIds,
                'Unexpected carrier references in product shipping information'
            );

            unset($data['carriers']);
        }

        $this->assertNumberShippingFields($data, $productShippingInformation);
        $this->assertDeliveryTimeNotes($data, $productShippingInformation);

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided product shipping fields haven\'t been asserted: %s', var_export($data, true)));
    }

    /**
     * @param array $data
     * @param ProductShippingInformation $productShippingInformation
     */
    private function assertDeliveryTimeNotes(array &$data, ProductShippingInformation $productShippingInformation): void
    {
        $notesTypeNamedValues = [
            'none' => DeliveryTimeNoteType::TYPE_NONE,
            'default' => DeliveryTimeNoteType::TYPE_DEFAULT,
            'specific' => DeliveryTimeNoteType::TYPE_SPECIFIC,
        ];

        if (isset($data['delivery time notes type'])) {
            $expectedType = $notesTypeNamedValues[$data['delivery time notes type']];
            $actualType = $productShippingInformation->getDeliveryTimeNoteType();
            Assert::assertEquals($expectedType, $actualType, 'Unexpected delivery time notes type value');

            unset($data['delivery time notes type']);
        }

        if (isset($data['delivery time in stock notes'])) {
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeInStockNotes();
            Assert::assertEquals(
                $data['delivery time in stock notes'],
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time in stock notes'
            );

            unset($data['delivery time in stock notes']);
        }

        if (isset($data['delivery time out of stock notes'])) {
            $actualLocalizedOutOfStockNotes = $productShippingInformation->getLocalizedDeliveryTimeOutOfStockNotes();
            Assert::assertEquals(
                $data['delivery time out of stock notes'],
                $actualLocalizedOutOfStockNotes,
                'Unexpected product delivery time out of stock notes'
            );

            unset($data['delivery time out of stock notes']);
        }
    }
}
