<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Command;

use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeImportSource;

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
     * @param ThemeImportSource $importSource
     */
    public function __construct(ThemeImportSource $importSource)
    {
        $this->importSource = $importSource;
    }

    /**
     * @return ThemeImportSource
     */
    public function getImportSource()
    {
        return $this->importSource;
    }
}
