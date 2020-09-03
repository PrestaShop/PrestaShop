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

use Behat\Gherkin\Node\TableNode;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new attachment :path
     *
     * @param string $path
     */
    public function addAttachment(string $path): void
    {
        $filePath = $this->getFullPathForDummyFile($path);
        $filename = pathinfo($filePath, PATHINFO_BASENAME);
        $mimeType = mime_content_type($filePath);
        $size = filesize($filePath);
        $tmp = sys_get_temp_dir() . '/' . uniqid('testimg');
        copy($filePath, $tmp);
        chmod($tmp, 0777);

        try {
            $command = new AddAttachmentCommand([1 => 'test1'], [1 => 'testdesc1']);
            $command->setFileInformation(
                $tmp,
                $size,
                $mimeType,
                $filename
            );

            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            dump($e);
        }
    }

    /**
     * @Given file :path exists
     *
     * @param string $path file path inside the dummyFile directory
     */
    public function assertFileExists(string $path): void
    {
        $fullPath = $this->getFullPathForDummyFile($path);

        Assert::assertTrue(
            is_file($fullPath),
            sprintf ('File "%s" does not exist.', $fullPath)
        );
    }
}
