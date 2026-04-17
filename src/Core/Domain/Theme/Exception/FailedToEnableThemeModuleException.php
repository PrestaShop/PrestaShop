<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Exception;

class FailedToEnableThemeModuleException extends ThemeException
{
    /**
     * @var string
     */
    private $moduleName;

    public function __construct(
        $moduleName,
        $message = '',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
