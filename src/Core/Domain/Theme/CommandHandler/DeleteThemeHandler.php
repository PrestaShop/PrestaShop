<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\DeleteThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\CannotDeleteThemeException;

/**
 * Class DeleteThemeHandler
 */
#[AsCommandHandler]
final class DeleteThemeHandler implements DeleteThemeHandlerInterface
{
    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * @param DeleteThemeCommand $command
     */
    public function handle(DeleteThemeCommand $command)
    {
        $plainThemeName = $command->getThemeName()->getValue();

        if (!$this->themeManager->uninstall($plainThemeName)) {
            throw new CannotDeleteThemeException(sprintf('Theme "%s" is used and thus cannot be deleted.', $plainThemeName));
        }
    }
}
