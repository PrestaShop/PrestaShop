<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Import\File;

use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use Symfony\Component\Finder\Finder;

/**
 * Class responsible for finding import files.
 */
final class FileFinder
{
    /**
     * @var ImportDirectory
     */
    private $importDirectory;

    public function __construct(ImportDirectory $importDirectory)
    {
        $this->importDirectory = $importDirectory;
    }

    /**
     * Get import file names in import directory.
     *
     * @return array|string[]
     */
    public function getImportFileNames()
    {
        if (!$this->importDirectory->isReadable()) {
            return [];
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->importDirectory->getDir())
            ->notName('/^index\.php/i');

        $fileNames = [];

        foreach ($finder as $file) {
            $fileNames[] = $file->getFilename();
        }

        return $fileNames;
    }
}
