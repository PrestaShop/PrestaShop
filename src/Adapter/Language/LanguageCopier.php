<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use PrestaShop\PrestaShop\Core\Language\Copier\LanguageCopierConfigInterface;
use PrestaShop\PrestaShop\Core\Language\Copier\LanguageCopierInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LanguageCopier responsible for copying a language into another language
 */
final class LanguageCopier implements LanguageCopierInterface
{
    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param LanguageDataProvider $languageDataProvider
     * @param TranslatorInterface $translator
     * @param Filesystem $filesystem
     */
    public function __construct(
        LanguageDataProvider $languageDataProvider,
        TranslatorInterface $translator,
        Filesystem $filesystem
    ) {
        $this->languageDataProvider = $languageDataProvider;
        $this->translator = $translator;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function copyLanguage(LanguageCopierConfigInterface $config)
    {
        $languageFiles = $this->languageDataProvider->getFilesList(
            $config->getLanguageFrom(),
            $config->getThemeFrom(),
            $config->getLanguageTo(),
            $config->getThemeTo(),
            true
        );

        foreach ($languageFiles as $source => $destination) {
            try {
                $this->filesystem->mkdir(dirname($destination));
            } catch (IOExceptionInterface $exception) {
                $this->errors[] = $this->translator->trans(
                    'Cannot create the folder "%folder%". Please check your directory writing permissions.',
                    [
                        '%folder%' => $destination,
                    ],
                    'Admin.International.Notification'
                );
                continue;
            }

            try {
                $this->filesystem->copy($source, $destination);
            } catch (IOExceptionInterface $exception) {
                $this->errors[] = $this->translator->trans(
                    'Impossible to copy "%source%" to "%dest%".',
                    [
                        '%source%' => $source,
                        '%dest%' => $destination,
                    ],
                    'Admin.International.Notification'
                );
                continue;
            }

            if ($this->isModuleContext($source, $destination, $config->getLanguageFrom())) {
                $changedModuleTranslationKeys = $this->changeModulesTranslationKeys(
                    $destination,
                    $config->getThemeFrom(),
                    $config->getThemeTo()
                );

                if (!$changedModuleTranslationKeys) {
                    $this->errors[] = $this->translator->trans(
                        'Impossible to translate "%dest%".',
                        [
                            '%dest%' => $destination,
                        ],
                        'Admin.International.Notification'
                    );
                }
            }
        }

        if (!empty($this->errors)) {
            $this->errors[] = $this->translator->trans(
                'A part of the data has been copied but some of the language files could not be found.',
                [],
                'Admin.International.Notification'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Checks if the source and destination paths are related to modules
     *
     * @param string $source
     * @param string $destination
     * @param string $language
     *
     * @return bool
     */
    private function isModuleContext($source, $destination, $language)
    {
        // Legacy condition
        return false !== strpos($destination, 'modules') && basename($source) === $language.'.php';
    }

    /**
     * A legacy method to change modules translation keys
     *
     * @param string $path
     * @param string $themeFrom
     * @param string $themeTo
     *
     * @return bool result
     */
    private function changeModulesTranslationKeys($path, $themeFrom, $themeTo)
    {
        $content = file_get_contents($path);
        $arrayReplace = [];
        $result = true;

        if (preg_match_all('#\$_MODULE\[\'([^\']+)\'\]#Ui', $content, $matches)) {
            foreach ($matches[1] as $value) {
                $arrayReplace[$value] = str_replace($themeFrom, $themeTo, $value);
            }

            $content = str_replace(array_keys($arrayReplace), array_values($arrayReplace), $content);
            $result = file_put_contents($path, $content) === false ? false : true;
        }

        return $result;
    }
}
