<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
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
        ?Throwable $previous = null
    ) {
        $this->allowedSizeBytes = $allowedSizeBytes;
        parent::__construct($message, $code, $previous);
    }
}
