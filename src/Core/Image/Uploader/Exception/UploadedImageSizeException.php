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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Image\Uploader\Exception;

use Throwable;

class UploadedImageSizeException extends ImageUploadException
{
    /**
     * @var int
     */
    private $allowedSizeBytes;

    /**
     * @param int $allowedSizeBytes
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return self
     */
    public static function build(
        int $allowedSizeBytes,
        string $message = null,
        int $code = 0,
        Throwable $previous = null
    ): self {
        if (null === $message) {
            $message = sprintf(
                'Max file size allowed is "%s" bytes.',
                $allowedSizeBytes
            );
        }

        return new self(
            $allowedSizeBytes,
            $message,
            $code,
            $previous
        );
    }

    /**
     * @return int
     */
    public function getAllowedSizeBytes(): int
    {
        return $this->allowedSizeBytes;
    }

    /**
     * @param int $allowedSizeBytes
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    private function __construct(
        int $allowedSizeBytes,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->allowedSizeBytes = $allowedSizeBytes;
        parent::__construct($message, $code, $previous);
    }
}
