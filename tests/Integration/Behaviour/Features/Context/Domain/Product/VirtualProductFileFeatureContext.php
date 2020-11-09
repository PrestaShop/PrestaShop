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
use DateTime;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class VirtualProductFileFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then virtual product :productReference should not have a file
     *
     * @param string $productReference
     */
    public function assertProductHasNoFile(string $productReference): void
    {
        $virtualProductFile = $this->getProductForEditing($productReference)->getVirtualProductFile();

        Assert::assertEquals(null, $virtualProductFile, 'Expected no virtual product file');
    }

    /**
     * @When I add new virtual product :productReference file :fileReference with following details:
     *
     * @param string $productReference
     * @param string $fileReference
     * @param TableNode $dataTable
     */
    public function addFile(string $productReference, string $fileReference, TableNode $dataTable): void
    {
        $dataRows = $dataTable->getRowsHash();
        $filePath = $this->uploadDummyFile($dataRows['file name']);

        $virtualProductId = $this->getCommandBus()->handle(new AddVirtualProductFileCommand(
            $this->getSharedStorage()->get($productReference),
            $filePath,
            $dataRows['display name'],
            PrimitiveUtils::castStringIntegerIntoInteger($dataRows['access days']),
            PrimitiveUtils::castStringIntegerIntoInteger($dataRows['download times limit']),
            new DateTime($dataRows['expiration date'])
        ));

        $this->getSharedStorage()->set($fileReference, $virtualProductId->getValue());
    }

    /**
     * @Then virtual product :productReference should have a file :fileReference with following details:
     *
     * @param string $productReference
     * @param string $fileReference
     * @param TableNode $dataTable
     */
    public function assertFile(string $productReference, string $fileReference, TableNode $dataTable): void
    {
        $actualFile = $this->getProductForEditing($productReference)->getVirtualProductFile();
        if (!$actualFile) {
            throw new RuntimeException('Expected virtual product to have a file');
        }

        $dataRows = $dataTable->getRowsHash();
        Assert::assertEquals(
            $dataRows['display name'],
            $actualFile->getDisplayName(),
            'Unexpected display file name'
        );
        Assert::assertEquals(
            $dataRows['file name'],
            $actualFile->getFileName(),
            'Unexpected file name'
        );
        Assert::assertEquals(
            PrimitiveUtils::castStringIntegerIntoInteger($dataRows['access days']),
            $actualFile->getAccessDays(),
            'Unexpected file access days'
        );
        Assert::assertEquals(
            PrimitiveUtils::castStringIntegerIntoInteger($dataRows['download times limit']),
            $actualFile->getDownloadTimesLimit(),
            'Unexpected file download times limit'
        );
        Assert::assertEquals(
            $dataRows['expiration date'],
            //@todo: handle optional cases
            $actualFile->getExpirationDate()->format(DateTimeUtil::DEFAULT_FORMAT),
            'Unexpected file download times limit'
        );
    }

    //@todo: use dummyFileUploader from PR https://github.com/PrestaShop/PrestaShop/pull/21510
    private function uploadDummyFile(string $dummyFileName): string
    {
        $destination = tempnam(sys_get_temp_dir(), 'TEST_PS_');
        copy(_PS_ROOT_DIR_ . 'tests/Resources/dummyFile/' . $dummyFileName, tempnam(sys_get_temp_dir(), 'TEST_PS_'));

        return $destination;
    }
}
