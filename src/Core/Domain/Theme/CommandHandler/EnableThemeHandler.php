<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\EnableThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\CannotEnableThemeException;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ThemeConstraintException;

/**
 * Class EnableThemeHandler
 */
#[AsCommandHandler]
final class EnableThemeHandler implements EnableThemeHandlerInterface
{
    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var CacheClearerInterface
     */
    private $smartyCacheClearer;

    /**
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @param ThemeManager $themeManager
     * @param CacheClearerInterface $smartyCacheClearer
     * @param bool $isSingleShopContext
     */
    public function __construct(
        ThemeManager $themeManager,
        CacheClearerInterface $smartyCacheClearer,
        $isSingleShopContext
    ) {
        $this->themeManager = $themeManager;
        $this->smartyCacheClearer = $smartyCacheClearer;
        $this->isSingleShopContext = $isSingleShopContext;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotEnableThemeException
     * @throws ThemeConstraintException
     */
    public function handle(EnableThemeCommand $command)
    {
        if (!$this->isSingleShopContext) {
            throw new ThemeConstraintException('Themes can be changed only in single shop context', ThemeConstraintException::RESTRICTED_ONLY_FOR_SINGLE_SHOP);
        }

        $plainThemeName = $command->getThemeName()->getValue();

        if (!$this->themeManager->enable($plainThemeName)) {
            $errors = $this->themeManager->getErrors($plainThemeName);

            if (is_array($errors)) {
                $error = reset($errors);
            } elseif ($errors) {
                $error = $errors;
            } else {
                // handle bad error usecases
                $error = '';
            }

            throw new CannotEnableThemeException($error);
        }

        $this->smartyCacheClearer->clear();
    }
}
