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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates/updates attachment from data submitted in category form
 */
final class AttachmentFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->createAddAttachmentCommand($data);

        /** @var AttachmentId $attachmentId */
        $attachmentId = $this->commandBus->handle($command);

        return $attachmentId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     */
    public function update($id, array $data)
    {
        $attachmentIdObject = new AttachmentId((int) $id);

        $command = $this->createEditAttachmentCommand($attachmentIdObject, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Creates edit attachment command from
     *
     * @param AttachmentId $attachmentId
     * @param array $data
     *
     * @return EditAttachmentCommand
     */
    private function createEditAttachmentCommand(
        AttachmentId $attachmentId,
        array $data
    ): EditAttachmentCommand {
        /** @var UploadedFile|null $fileObject */
        $fileObject = $data['file'];

        $command = new EditAttachmentCommand($attachmentId);
        $command->setLocalizedNames($data['name']);
        $command->setLocalizedDescriptions($data['file_description']);

        if ($fileObject instanceof UploadedFile) {
            $command->setFileInfo(
                $fileObject->getPathname(),
                $fileObject->getMimeType(),
                $fileObject->getClientOriginalName(),
                $fileObject->getSize()
            );
        }

        return $command;
    }

    /**
     * @param array $data
     *
     * @return AddAttachmentCommand
     */
    private function createAddAttachmentCommand(array $data)
    {
        $addAttachmentCommand = new AddAttachmentCommand(
            $data['name'],
            $data['file_description']
        );

        if (isset($data['file']) && $data['file'] !== null) {
            /** @var UploadedFile $file */
            $file = $data['file'];

            $addAttachmentCommand->setFileInformation(
                $file->getPathname(),
                $file->getSize(),
                $file->getMimeType(),
                $file->getClientOriginalName()
            );
        }

        return $addAttachmentCommand;
    }
}
