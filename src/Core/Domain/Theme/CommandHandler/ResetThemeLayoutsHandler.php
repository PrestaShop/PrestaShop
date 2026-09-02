<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\ResetThemeLayoutsCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\CannotResetThemeLayoutsException;

/**
 * Class ResetThemeLayoutsHandler
 */
#[AsCommandHandler]
final class ResetThemeLayoutsHandler implements ResetThemeLayoutsHandlerInterface
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
     * {@inheritdoc}
     */
    public function handle(ResetThemeLayoutsCommand $command)
    {
        if (!$this->themeManager->reset($command->getThemeName()->getValue())) {
            throw new CannotResetThemeLayoutsException(sprintf('Cannot reset "%s" theme page layouts.', $command->getThemeName()->getValue()));
        }
    }
}
