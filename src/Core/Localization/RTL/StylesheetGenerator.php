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

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use CSSJanus;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
use PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException;
use Tools;

/**
 * Creates RTL versions of LTR CSS files.
 *
 * This class creates new files based on the original ones by using CSSJanus first,
 * then applying an optional .rtlfix file, if one with the same name as the processed file is found.
 *
 * Inspired by "Localize Fixture" from Mahdi Shad @ iPresta
 *
 * @see https://github.com/iPresta/localize-fixture
 */
class StylesheetGenerator
{
    /**
     * Default file type to look up.
     */
    public const DEFAULT_FILE_TYPE = 'css';

    /**
     * Default suffix to use for RTL transformed files.
     */
    public const DEFAULT_RTL_SUFFIX = '_rtl';

    /**
     * Extension of RTL fix files.
     */
    public const RTLFIX_EXTENSION = 'rtlfix';

    /**
     * @var string
     */
    private $fileType;

    /**
     * @var string
     */
    private $rtlSuffix;

    /**
     * @param string $fileType [default='css'] File type (CSS or SCSS)
     * @param string $rtlSuffix [default='_rtl'] Suffix to add to transformed RTL files
     */
    public function __construct($fileType = self::DEFAULT_FILE_TYPE, $rtlSuffix = self::DEFAULT_RTL_SUFFIX)
    {
        $this->fileType = $fileType;
        $this->rtlSuffix = $rtlSuffix;
    }

    /**
     * Creates an RTL version of all the files in the selected path recursively.
     *
     * @param string $directory Path to process. All CSS files in this directory will be processed.
     * @param bool $regenerate [default=false] Indicates if RTL files should be re-generated even if they exist
     *
     * @throws GenerationException
     */
    public function generateInDirectory($directory, $regenerate = false)
    {
        $allFiles = $this->getFilesInDirectory($directory);

        foreach ($allFiles as $file) {
            if ($this->shouldProcessFile($directory . '/' . $file, $regenerate)) {
                $this->processFile($directory . '/' . $file);
            }
        }
    }

    /**
     * Indicates if a file should be processed or not.
     *
     * @param string $file File path
     * @param bool $regenerate Indicates if RTL files should be re-generated even if they exist
     *
     * @return bool
     */
    private function shouldProcessFile($file, $regenerate)
    {
        return
            strpos($file, '/node_modules/') === false
            // does not end with .rtlfix
            && substr(rtrim($file, '.' . $this->fileType), -4) !== $this->rtlSuffix
            // RTL file does not exist or we are regenerating them
            && ($regenerate || !file_exists($this->getRtlFileName($file)));
    }

    /**
     * Creates an RTL version of a file.
     *
     * @param string $filePath Path to the file to process
     *
     * @throws GenerationException
     */
    private function processFile($filePath)
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new GenerationException(sprintf('Unable to read from CSS file: %s', $filePath));
        }

        $rendered = CSSJanus::transform($content);

        if (strlen($rendered) === 0 && strlen($content) !== 0) {
            throw new GenerationException(sprintf('Failed to generate RTL CSS from file: %s', $filePath));
        }

        $content = $this->appendRtlFixIfNecessary(
            $rendered,
            $filePath
        );

        $this->saveFile($content, $filePath);
    }

    /**
     * Creates a list of all files of the required type in the provided directory recursively.
     *
     * @param string $directory Directory to scan
     *
     * @return string[] Array of file paths, relative to the provided directory
     */
    private function getFilesInDirectory($directory)
    {
        return Tools::scandir($directory, $this->fileType, '', true);
    }

    /**
     * Removes the file extension from path.
     *
     * @param string $filePath Path to a file
     *
     * @return string
     */
    private function getFilePathWithoutExtension($filePath)
    {
        $path = pathinfo($filePath);

        return $path['dirname'] . '/' . $path['filename'];
    }

    /**
     * Returns the full path for the RTL filename corresponding to the provided base filename.
     *
     * @param string $baseFileName Base file name
     *
     * @return string RTL filename
     */
    private function getRtlFileName($baseFileName)
    {
        return $this->getFilePathWithoutExtension($baseFileName) . $this->rtlSuffix . '.' . $this->fileType;
    }

    /**
     * Appends the content of an .rtlfix file to $content.
     *
     * @param string $content Base content
     * @param string $baseFile Path to the processed file
     *
     * @return string Content with RTL fix applied
     *
     * @throws GenerationException If unable to read from .rtlfix file
     */
    private function appendRtlFixIfNecessary($content, $baseFile)
    {
        $filePath = $this->getFilePathWithoutExtension($baseFile);

        $rtlFixFilePath = $filePath . '.' . self::RTLFIX_EXTENSION;

        if (file_exists($rtlFixFilePath)) {
            $rtlFixContent = file_get_contents($rtlFixFilePath);

            if ($rtlFixContent === false) {
                throw new GenerationException(sprintf('Failed to read from file: %s', $rtlFixFilePath));
            }

            return $content . PHP_EOL . $rtlFixContent;
        }

        return $content;
    }

    /**
     * Saves $content the appropriate file based on the name of the original file.
     *
     * @param string $content Content to save
     * @param string $baseFile Name of the original file
     *
     * @throws GenerationException If unable to write to file
     */
    private function saveFile($content, $baseFile)
    {
        $rtlFilePath = $this->getRtlFileName($baseFile);

        if (false === file_put_contents($rtlFilePath, $content)) {
            throw new GenerationException(sprintf('Unable to write file to: %s', $rtlFilePath));
        }

        @chmod($rtlFilePath, FileSystem::DEFAULT_MODE_FILE);
    }
}
