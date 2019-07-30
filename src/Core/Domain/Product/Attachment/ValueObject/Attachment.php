<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Exception\ProductAttachmentException;
use SplFileObject;
use function strlen;

/**
 * Add files that customers can download directly on the product page (instructions, manual, recipe, etc.).
 */
final class Attachment
{
    public const MAX_SIZE = 32;

    /**
     * @var string[]
     */
    private $localizedTitles;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var SplFileObject
     */
    private $file;

    /**
     *
     * @param string $filePath
     * @param array $localizedTitles
     * @param array $localizedDescriptions
     *
     * @throws ProductAttachmentException
     */
    public function __construct(
        string $filePath,
        array $localizedTitles,
        array $localizedDescriptions
    ) {
        $this->assertIsTitleLengthValid($localizedTitles);

        $file = new SplFileObject($filePath);
        $this->assertIsFile($file);

        $this->file = $file;
        $this->localizedTitles = $localizedTitles;
        $this->localizedDescriptions = $localizedDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedTitles(): array
    {
        return $this->localizedTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return SplFileObject
     */
    public function getFile(): SplFileObject
    {
        return $this->file;
    }

    /**
     * @param array $localizedTitles
     *
     * @throws ProductAttachmentException
     */
    private function assertIsTitleLengthValid(array $localizedTitles): void
    {
        foreach ($localizedTitles as $langId => $title) {
            if (strlen($title) > self::MAX_SIZE) {
                throw new ProductAttachmentException(
                    sprintf(
                        'Product attachment title "%s" has breached maximum allowed size of %d for language id %d',
                        $title,
                        self::MAX_SIZE,
                        $langId
                    ),
                    ProductAttachmentException::INVALID_ATTACHMENT_TITLE
                );
            }
        }
    }

    /**
     * @param SplFileObject $file
     *
     * @throws ProductAttachmentException
     */
    private function assertIsFile(SplFileObject $file): void
    {
        if (!$file->isFile()) {
            throw new ProductAttachmentException(
                sprintf(
                    'File "%s" is not a regular file',
                    $file->getPathname()
                ),
                ProductAttachmentException::INVALID_FILE
            );
        }
    }
}
