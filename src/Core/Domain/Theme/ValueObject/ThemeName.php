<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\InvalidThemeNameException;

/**
 * Class ThemeName
 */
class ThemeName
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @param string $themeName
     */
    public function __construct($themeName)
    {
        $this->assertThemeNameIsNotEmptyString($themeName);
        $this->assertThemeNameMatchesPattern($themeName);

        $this->themeName = $themeName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->themeName;
    }

    /**
     * @param string $themeName
     *
     * @throws InvalidThemeNameException
     */
    private function assertThemeNameIsNotEmptyString($themeName)
    {
        if (!is_string($themeName) || empty($themeName)) {
            throw new InvalidThemeNameException('Theme name cannot be empty.');
        }
    }

    /**
     * @param string $themeName
     *
     * @throws InvalidThemeNameException
     */
    private function assertThemeNameMatchesPattern($themeName)
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $themeName)) {
            throw new InvalidThemeNameException(sprintf('Invalid theme name %s provided.', var_export($themeName, true)));
        }
    }
}
