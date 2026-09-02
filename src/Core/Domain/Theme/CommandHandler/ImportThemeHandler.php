<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\Exception\ThemeAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeUploaderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\ImportThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ImportedThemeAlreadyExistsException;
use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeImportSource;
use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeName;

/**
 * Class ImportThemeHandler
 */
#[AsCommandHandler]
final class ImportThemeHandler implements ImportThemeHandlerInterface
{
    /**
     * @var ThemeUploaderInterface
     */
    private $themeUploader;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ThemeUploaderInterface $themeUploader
     * @param ThemeManager $themeManager
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ThemeUploaderInterface $themeUploader,
        ThemeManager $themeManager,
        ConfigurationInterface $configuration
    ) {
        $this->themeUploader = $themeUploader;
        $this->themeManager = $themeManager;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ImportThemeCommand $command)
    {
        $type = $command->getImportSource()->getSourceType();
        $source = $command->getImportSource()->getSource();

        $themePath = '';
        if (ThemeImportSource::FROM_ARCHIVE === $type) {
            $themePath = $this->themeUploader->upload($source);
        } elseif (ThemeImportSource::FROM_WEB === $type) {
            $themePath = $source;
        } elseif (ThemeImportSource::FROM_FTP === $type) {
            $themePath = $this->configuration->get('_PS_ALL_THEMES_DIR_') . $source;
        }

        try {
            $this->themeManager->install($themePath);
        } catch (ThemeAlreadyExistsException $e) {
            throw new ImportedThemeAlreadyExistsException(
                new ThemeName($e->getThemeName()),
                sprintf('Imported theme "%s" already exists.', $e->getThemeName()),
                0,
                $e
            );
        } finally {
            if (ThemeImportSource::FROM_ARCHIVE === $type) {
                @unlink($themePath);
            }
        }
    }
}
