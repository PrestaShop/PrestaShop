<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeProviderInterface;
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
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @param ThemeManager $themeManager
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(ThemeManager $themeManager, ThemeProviderInterface $themeProvider)
    {
        $this->themeManager = $themeManager;
        $this->themeProvider = $themeProvider;
    }

    /**
     * @param DeleteThemeCommand $command
     */
    public function handle(DeleteThemeCommand $command)
    {
        $plainThemeName = $command->getThemeName()->getValue();

        if (in_array($plainThemeName, $this->themeProvider->getNotDeletableThemes())) {
            throw new CannotDeleteThemeException(sprintf('Theme "%s" is used and thus cannot be deleted.', $plainThemeName));
        }

        if (!$this->themeManager->uninstall($plainThemeName)) {
            throw new CannotDeleteThemeException(sprintf('Theme "%s" is used and thus cannot be deleted.', $plainThemeName));
        }
    }
}
