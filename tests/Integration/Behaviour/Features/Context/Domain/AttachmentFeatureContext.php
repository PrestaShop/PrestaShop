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

class AttachmentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new attachment :reference with following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function addAttachment(string $reference, TableNode $tableNode)
    {
        $data = $tableNode->getRowsHash();

        $attachment = new Attachment();
        $attachment->description = $this->parseLocalizedArray($data['description']);
        $attachment->name = $this->parseLocalizedArray($data['name']);
        $attachment->file_name = $data['file_name'];
        $attachment->mime = $data['mime'];
        $attachment->file = sha1(uniqid());

        $attachment->add();

        $this->getSharedStorage()->set($reference, (int) $attachment->id);
    }
}
