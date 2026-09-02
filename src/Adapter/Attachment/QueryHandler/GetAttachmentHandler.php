<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler\GetAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\Attachment;

/**
 * Provides path and original file name of attachment
 */
#[AsQueryHandler]
final class GetAttachmentHandler extends AbstractAttachmentHandler implements GetAttachmentHandlerInterface
{
    /**
     * @var string
     */
    private $downloadDirectory;

    /**
     * @param string $downloadDirectory
     */
    public function __construct(string $downloadDirectory)
    {
        $this->downloadDirectory = $downloadDirectory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentNotFoundException
     */
    public function handle(GetAttachment $query): Attachment
    {
        $attachment = $this->getAttachment($query->getAttachmentId());
        $path = $this->downloadDirectory . $attachment->file;

        if (!file_exists($path)) {
            throw new AttachmentNotFoundException(sprintf('Attachment file was not found at %s', $path));
        }

        return new Attachment(
            $path,
            $attachment->file_name
        );
    }
}
