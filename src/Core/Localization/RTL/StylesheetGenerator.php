<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use Exception;
use PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException;
use PrestaShop\RtlCss\RtlCss;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use \Tools;

/**
 * Creates RTL versions of LTR CSS files.
 *
 * This class creates new files based on the original ones by using CSSJanus first,
 * then applying an optional .rtlfix file, if one with the same name as the processed file is found.
 *
 * Inspired by "Localize Fixture" from Mahdi Shad @ iPresta
 * @link https://github.com/iPresta/localize-fixture
 */
class StylesheetGenerator
{

    /**
     * Default file type to look up
     */
    const DEFAULT_FILE_TYPE = 'css';

    /**
     * Default suffix to use for RTL transformed files
     */
    const DEFAULT_RTL_SUFFIX = '_rtl';

    /**
     * Extension of RTL fix files
     */
    const RTLFIX_EXTENSION = 'rtlfix';

    /**
     * @var string
     */
    private $fileType;

    /**
     * @var string
     */
    private $rtlSuffix;

    /**
     * @var OutputFormat
     */
    private $outputFormat;

    /**
     * @param string $fileType [default='css'] File type (CSS or SCSS)
     * @param string $rtlSuffix [default='_rtl'] Suffix to add to transformed RTL files
     */
    public function __construct($fileType = self::DEFAULT_FILE_TYPE, $rtlSuffix = self::DEFAULT_RTL_SUFFIX)
    {
        $this->fileType = $fileType;
        $this->rtlSuffix = $rtlSuffix;
        $this->outputFormat = OutputFormat::createCompact()
            ->setKeepComments(true);
    }

    /**
     * Creates an RTL version of all the files in the selected path recursively.
     *
     * @param string $directory Path to process. All CSS files in this directory will be processed.
     * @param bool $regenerate [default=false] Indicates if RTL files should be re-generated even if they exist
     *
     * @throws GenerationException
     */
    public function generateFromDirectory($directory, $regenerate = false)
    {
        $allFiles = $this->getFilesInDirectory($directory);

        foreach ($allFiles as $file) {
            if ($this->shouldProcessFile($directory.'/'.$file, $regenerate)) {
                $this->processFile($directory.'/'.$file);
            }
        }
    }

    /**
     * Indicates if a file should be processed or not
     *
     * @param string $file File path
     * @param bool $regenerate Indicates if RTL files should be re-generated even if they exist
     *
     * @return bool
     */
    private function shouldProcessFile($file, $regenerate)
    {
        return (
            strpos($file, '/node_modules/') === false
            // does not end with .rtlfix
            && substr(rtrim($file, '.'.$this->fileType), -4) !== $this->rtlSuffix
            // RTL file does not exist
            && (!$regenerate && !file_exists($this->getRtlFileName($file)))
        );
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
            throw new GenerationException(
                sprintf(
                    "Unable to read CSS file: %s",
                    $filePath
                )
            );
        }

        try {
            $parser = new Parser(
                $this->sanitizeContent($filePath, $content)
            );

            // free up memory
            unset($content);

            $tree = $parser->parse();

            (new RtlCss($tree))->flip();

            $rendered = $tree->render($this->outputFormat);

            unset($tree);
        } catch (Exception $e) {
            throw new GenerationException(
                sprintf("Failed to generate RTL CSS from file: %s", $filePath),
                0,
                $e
            );
        }

        $content = $this->appendRtlFixIfNecessary(
            $rendered,
            $filePath
        );

        $this->saveFile($content, $filePath);
    }

    /**
     * @param $filePath
     * @param $content
     *
     * @return string
     * @throws GenerationException
     */
    private function sanitizeContent($filePath, $content)
    {
        return $this->fixSassBug(
            $filePath,
            $this->removeBomIfPresent($content)
        );
    }

    /**
     * Removes Byte Order Mark from CSS code if found
     *
     * @param string $content CSS code
     *
     * @return string Sanitized CSS code
     *
     * @throws GenerationException If removal fails for any reason
     */
    private function removeBomIfPresent($content)
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        if ($content === null) {
            throw new GenerationException(
                sprintf(
                    "Failed to remove Byte order mark from CSS content, PCRE error code: %s",
                    preg_last_error()
                )
            );
        }

        return $content;
    }

    /**
     * Fixes invalid CSS produced by a bug from Sass
     *
     * @param string $filePath Path to the file that is being processed
     * @param string $content CSS content
     *
     * @return string Fixed CSS content
     */
    private function fixSassBug($filePath, $content)
    {
        // Sass misinterprets "@viewport" nested inside a selector
        if (preg_match('/admin-theme(?:-[\w]+)*.css/', $filePath) !== false) {
            return str_replace(
                '@-ms-viewport{.bootstrap{width:device-width}}',
                '.bootstrap @-ms-viewport{width:device-width}',
                $content
            );
        }

        return $content;
    }

    /**
     * Creates a list of all files of the required type in the provided directory recursively.
     *
     * @param string $directory Directory to scan
     *
     * @return string[] Array of file paths, relative to the provided directory
     */
    private function getFilesInDirectory($directory) {
        return Tools::scandir($directory, $this->fileType, '', true);
    }

    /**
     * Removes the file extension from path
     *
     * @param string $filePath Path to a file
     *
     * @return string
     */
    private function getFilePathWithoutExtension($filePath)
    {
        $path = pathinfo($filePath);
        return $path['dirname'].'/'.$path['filename'];
    }

    /**
     * Returns the full path for the RTL filename corresponding to the provided base filename
     *
     * @param string $baseFileName Base file name
     *
     * @return string RTL filename
     */
    private function getRtlFileName($baseFileName)
    {
        return $this->getFilePathWithoutExtension($baseFileName).$this->rtlSuffix.'.'.$this->fileType;
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

        $rtlFixFilePath = $filePath.'.'.self::RTLFIX_EXTENSION;

        if (file_exists($rtlFixFilePath)) {
            $rtlFixContent = file_get_contents($rtlFixFilePath);

            if ($rtlFixContent === false) {
                throw new GenerationException(
                    sprintf(
                        "Failed to read from file: %s",
                        $rtlFixFilePath
                    )
                );
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

        if (file_exists($rtlFilePath)) {
            unlink($rtlFilePath);
        }

        if (false === file_put_contents($rtlFilePath, $content)) {
            throw new GenerationException(
                sprintf(
                    "Unable to write file: %s",
                    $rtlFilePath
                )
            );
        }

        @chmod($rtlFilePath, 0644);
    }
}
