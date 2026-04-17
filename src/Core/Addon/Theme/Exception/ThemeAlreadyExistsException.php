<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Class ThemeAlreadyExistsException
 */
class ThemeAlreadyExistsException extends CoreException
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @param string $themeName
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($themeName, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->themeName = $themeName;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }
}
