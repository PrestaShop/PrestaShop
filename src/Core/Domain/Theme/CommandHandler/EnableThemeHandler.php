<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\EnableThemeCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\CannotEnableThemeException;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ThemeConstraintException;

/**
 * Class EnableThemeHandler
 */
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
