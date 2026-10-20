<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Attachment;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;

class AttachmentForEditingTest extends TestCase
{
    public function testEditableAttachmentExposesItsId(): void
    {
        $editableAttachment = new EditableAttachment(42, 'doc.pdf', [1 => 'Doc'], [1 => 'Description']);

        $this->assertSame(42, $editableAttachment->getAttachmentId());
        $this->assertSame('doc.pdf', $editableAttachment->getFileName());
    }

    public function testGetAttachmentForEditingAcceptsAScalarId(): void
    {
        $query = new GetAttachmentForEditing(42);

        $this->assertSame(42, $query->getAttachmentId()->getValue());
    }

    public function testEditAttachmentCommandAcceptsAScalarId(): void
    {
        $command = new EditAttachmentCommand(42);

        $this->assertSame(42, $command->getAttachmentId()->getValue());
    }
}
