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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateCustomizationFieldsFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference with following customization fields:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateCustomizationFields(string $productReference, TableNode $table)
    {
        $customizationFields = $this->localizeByColumns($table);
        $fieldsForUpdate = [];
        $fieldReferences = [];

        foreach ($customizationFields as $customizationField) {
            $addedByModule = isset($customizationField['added by module']) ?
                PrimitiveUtils::castStringBooleanIntoBoolean($customizationField['added by module']) :
                false;
            $fieldReference = $customizationField['reference'];
            $id = $this->getSharedStorage()->exists($fieldReference) ? $this->getSharedStorage()->get($fieldReference) : null;

            $fieldReferences[] = $fieldReference;
            $fieldsForUpdate[] = [
                'id' => $id,
                'type' => $customizationField['type'] === 'file' ? CustomizationFieldType::TYPE_FILE : CustomizationFieldType::TYPE_TEXT,
                'localized_names' => $customizationField['name'],
                'is_required' => PrimitiveUtils::castStringBooleanIntoBoolean($customizationField['is required']),
                'added_by_module' => $addedByModule,
            ];
        }

        $this->updateProductCustomizationFields(
            $productReference,
            $fieldReferences,
            $fieldsForUpdate
        );
    }

    /**
     * @When I update product :productReference customization field name with text containing :nameLength symbols
     *
     * @param string $productReference
     * @param int $nameLength
     */
    public function addCustomizationFieldWithTooLongName(string $productReference, int $nameLength)
    {
        $fieldsForUpdate = [];
        foreach (Language::getIDs() as $langId) {
            $langId = (int) $langId;
            $fieldsForUpdate[] = [
                'id' => null,
                'type' => CustomizationFieldType::TYPE_TEXT,
                'is_required' => false,
                'added_by_module' => false,
                'localized_names' => [
                    $langId => PrimitiveUtils::generateRandomString($nameLength),
                ],
            ];
        }

        try {
            $this->updateProductCustomizationFields($productReference, ['name'], $fieldsForUpdate);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all customization fields from product :productReference
     *
     * @param string $productReference
     */
    public function updateCustomizationFieldsWithEmptyArray(string $productReference)
    {
        try {
            $this->getCommandBus()->handle(new RemoveAllCustomizationFieldsFromProductCommand(
                $this->getSharedStorage()->get($productReference)
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^product "(.+)" should (not be customizable|allow customization|require customization)$/
     *
     * @param string $productReference
     * @param string $customizability
     */
    public function assertCustomizability(string $productReference, string $customizability)
    {
        $customizationOptions = $this->getProductForEditing($productReference)->getCustomizationOptions();

        switch ($customizability) {
            case 'not be customizable':
                Assert::assertTrue(
                    $customizationOptions->isNotCustomizable(),
                    sprintf('Expected product "%s" to be not customizable', $productReference)
                );

                break;
            case 'allow customization':
                Assert::assertTrue(
                    $customizationOptions->allowsCustomization(),
                    sprintf('Expected product "%s" to allow customization', $productReference)
                );

                break;
            case 'require customization':
                Assert::assertTrue(
                    $customizationOptions->requiresCustomization(),
                    sprintf('Expected product "%s" to require customization', $productReference)
                );

                break;
            default:
                throw new RuntimeException(sprintf('Invalid customizability "%s" provided in test scenario', $customizability));
        }
    }

    /**
     * @Then product :productReference should have following customization fields:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertCustomizationFields(string $productReference, TableNode $table)
    {
        $data = $this->localizeByColumns($table);
        /** @var CustomizationField[] $actualFields */
        $actualFields = $this->getProductCustomizationFields($productReference);
        $notFoundExpectedFields = [];

        foreach ($data as $expectedField) {
            $expectedId = $this->getSharedStorage()->get($expectedField['reference']);
            $foundExpectedField = false;

            foreach ($actualFields as $key => $actualField) {
                if ($expectedId === $actualField->getCustomizationFieldId()) {
                    $foundExpectedField = true;
                    $expectedType = $expectedField['type'] === 'file' ? CustomizationFieldType::TYPE_FILE : CustomizationFieldType::TYPE_TEXT;
                    $expectedRequired = PrimitiveUtils::castStringBooleanIntoBoolean($expectedField['is required']);
                    Assert::assertEquals($expectedType, $actualField->getType(), 'Unexpected customization type');
                    Assert::assertEquals(
                        $expectedField['name'],
                        $actualField->getLocalizedNames(),
                        sprintf('Unexpected product "%s" customization field name', $productReference)
                    );

                    if ($expectedRequired !== $actualField->isRequired()) {
                        throw new RuntimeException(
                            sprintf(
                                'Expected customization field #%d to be %s',
                                $expectedId,
                                $expectedRequired ? 'required' : 'not required'
                            )
                        );
                    }

                    if (isset($expectedField['added by module'])) {
                        $expectedByModule = PrimitiveUtils::castStringBooleanIntoBoolean($expectedField['added by module']);
                        if ($expectedByModule !== $actualField->isAddedByModule()) {
                            throw new RuntimeException(
                                sprintf(
                                    'Expected customization field #%d to be added %s',
                                    $actualField->getCustomizationFieldId(),
                                    $expectedByModule ? 'by module' : 'not by module'
                                )
                            );
                        }
                    }
                    //unset this asserted customization field so we can check if there any left after loop
                    unset($actualFields[$key]);

                    continue;
                }
            }

            if (!$foundExpectedField) {
                $notFoundExpectedFields[] = $expectedField;
            }
        }

        if (!empty($notFoundExpectedFields)) {
            throw new RuntimeException(sprintf(
                'Following customization fields were not found for product %s: %s',
                $productReference,
                var_export($notFoundExpectedFields)
            ));
        }

        if (!empty($actualFields)) {
            throw new RuntimeException(sprintf(
                'Product "%s" contains unexpected customization fields: %s',
                $productReference,
                var_export($actualFields)
            ));
        }
    }

    /**
     * @Then product :productReference should have :expectedCount customizable :customizationType field(s)
     *
     * @param string $productReference
     * @param int $expectedCount
     * @param string $customizationType
     */
    public function assertCustomizationOptions(string $productReference, int $expectedCount, string $customizationType)
    {
        if (!in_array($customizationType, array_keys(CustomizationFieldType::AVAILABLE_TYPES))) {
            throw new RuntimeException(sprintf('Invalid customization type "%s" provided in test scenario', $customizationType));
        }

        $productForEditing = $this->getProductForEditing($productReference);

        if ('file' === $customizationType) {
            Assert::assertEquals(
                $expectedCount,
                $productForEditing->getCustomizationOptions()->getAvailableFileCustomizationsCount(),
                'Unexpected customizable file fields count'
            );
        } else {
            Assert::assertEquals(
                $expectedCount,
                $productForEditing->getCustomizationOptions()->getAvailableTextCustomizationsCount(),
                'Unexpected customizable text fields count'
            );
        }
    }

    /**
     * @Then I should get error that product customization field name is invalid
     */
    public function assertCustomizationFieldNameError(): void
    {
        $this->assertLastErrorIs(
            CustomizationFieldConstraintException::class,
            CustomizationFieldConstraintException::INVALID_NAME
        );
    }

    /**
     * @param string $productReference
     * @param array $fieldReferences
     * @param array $fieldsForUpdate
     */
    private function updateProductCustomizationFields(string $productReference, array $fieldReferences, array $fieldsForUpdate): void
    {
        try {
            $newCustomizationFieldIds = $this->getCommandBus()->handle(new SetProductCustomizationFieldsCommand(
                $this->getSharedStorage()->get($productReference),
                $fieldsForUpdate
            ));

            Assert::assertSameSize(
                $fieldReferences,
                $newCustomizationFieldIds,
                'Cannot set references in shared storage. References and actual customization fields doesn\'t match.'
            );

            /** @var CustomizationFieldId $customizationFieldId */
            foreach ($newCustomizationFieldIds as $key => $customizationFieldId) {
                $this->getSharedStorage()->set($fieldReferences[$key], $customizationFieldId->getValue());
            }
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
