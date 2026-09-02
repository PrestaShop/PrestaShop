<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Exception;

use PrestaShop\PrestaShop\Core\File\Exception\CannotUnlinkFileException;
use Throwable;

/**
 * Thrown when file unlink fails
 */
class CannotUnlinkAttachmentException extends CannotUnlinkFileException
{
    /**
     * @var string
     */
    private $filePath = '';

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', $code = 0, ?Throwable $previous = null, string $filePath = '')
    {
        parent::__construct($message, $code, $previous);
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
