<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
