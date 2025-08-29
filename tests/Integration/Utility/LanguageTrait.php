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

namespace Tests\Integration\Utility;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;

trait LanguageTrait
{
    protected static function addLanguageByLocale(string $locale): int
    {
        $isoCode = substr($locale, 0, strpos($locale, '-'));

        // Copy resource assets into tmp folder to mimic an upload file path
        $flagImage = __DIR__ . '/../../Resources/assets/lang/' . $isoCode . '.jpg';
        if (!file_exists($flagImage)) {
            $flagImage = __DIR__ . '/../../Resources/assets/lang/en.jpg';
        }

        $tmpFlagImage = sys_get_temp_dir() . '/' . $isoCode . '.jpg';
        $tmpNoPictureImage = sys_get_temp_dir() . '/' . $isoCode . '-no-picture.jpg';
        copy($flagImage, $tmpFlagImage);
        copy($flagImage, $tmpNoPictureImage);

        $command = new AddLanguageCommand(
            $locale,
            $isoCode,
            $locale,
            'd/m/Y',
            'd/m/Y H:i:s',
            $tmpFlagImage,
            $tmpNoPictureImage,
            false,
            true,
            [1]
        );

        $container = static::getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');

        return $commandBus->handle($command)->getValue();
    }
}
