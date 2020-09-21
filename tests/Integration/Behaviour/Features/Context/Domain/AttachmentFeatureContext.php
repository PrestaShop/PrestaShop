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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Attachment;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShopException;
use RuntimeException;

class AttachmentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new attachment :reference with following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function addAttachment(string $reference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        $fileName = $data['file_name'];
        $source = _PS_ROOT_DIR_ . '/tests/Integration/Behaviour/DummyFiles/' . $fileName;

        if (!is_file($source)) {
            throw new RuntimeException('%s is not a file', $source);
        }

        $destination = _PS_DOWNLOAD_DIR_ . $fileName;
        copy($source, $destination);

        $attachment = new Attachment();
        $attachment->description = $this->parseLocalizedArray($data['description']);
        $attachment->name = $this->parseLocalizedArray($data['name']);
        $attachment->file_name = $fileName;
        $attachment->mime = mime_content_type($destination);
        $attachment->file = pathinfo($destination, PATHINFO_BASENAME);

        $attachment->add();

        $this->getSharedStorage()->set($reference, (int) $attachment->id);
    }

    /**
     * @Then attachment :reference should have following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function assertAttachmentProperties(string $reference, TableNode $tableNode): void
    {
        $attachment = $this->getAttachment($reference);
        $data = $tableNode->getRowsHash();

        Assert::assertEquals($this->parseLocalizedArray($data['description']), $attachment->description);
        Assert::assertEquals($this->parseLocalizedArray($data['name']), $attachment->name);
        Assert::assertEquals($data['file_name'], $attachment->file_name);
        Assert::assertEquals($data['mime'], $attachment->mime);
        Assert::assertEquals($data['size'], $attachment->file_size);
    }

    /**
     * @param string $reference
     *
     * @return Attachment
     *
     * @throws PrestaShopException
     */
    private function getAttachment(string $reference): Attachment
    {
        $id = $this->getSharedStorage()->get($reference);
        $attachment = new Attachment($id);

        if (!$attachment->id) {
            throw new RuntimeException(sprintf('Failed to load attachment with id %d', $id));
        }

        return $attachment;
    }
}
