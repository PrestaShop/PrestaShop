<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Exception;

use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeName;
use Throwable;

/**
 * Thrown when trying to import already existing theme
 */
class ImportedThemeAlreadyExistsException extends ThemeException
{
    /**
     * @var ThemeName
     */
    private $themeName;

    /**
     * @param ThemeName $themeName
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(ThemeName $themeName, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->themeName = $themeName;
    }

    /**
     * @return ThemeName
     */
    public function getThemeName()
    {
        return $this->themeName;
    }
}
