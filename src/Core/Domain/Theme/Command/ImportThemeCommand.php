<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Command;

use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeImportSource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImportThemeCommand imports theme from given source.
 */
class ImportThemeCommand
{
    /**
     * @var ThemeImportSource
     */
    private $importSource;

    /**
     * @param ThemeImportSource|string $importSource
     * @param UploadedFile|string $source
     *
     * @deprecated Since 9.2 - The parameter $importSource will not support ThemeImportSource as type in 10.0
     */
    public function __construct(ThemeImportSource|string $importSource, UploadedFile|string|null $source = null)
    {
        $this->importSource = is_object($importSource) ? $importSource : ThemeImportSource::fromSourceTypeAndSource($importSource, $source);
    }

    /**
     * @return ThemeImportSource
     */
    public function getImportSource()
    {
        return $this->importSource;
    }
}
