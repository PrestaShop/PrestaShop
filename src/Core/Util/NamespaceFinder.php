<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Util;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Util\Exception\NamespaceNotFoundException;
use Symfony\Component\Finder\Finder;

class NamespaceFinder
{
    const NAMESPACE_REGEX = '~namespace[ \t]+(.+)[ \t]*;~Um';

    /**
     * @param string $folderPath
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function findNamespaceFromFolder(string $folderPath): string
    {
        $finder = new Finder();
        $finder->files()->in($folderPath)->name('*.php')->depth(0);
        foreach ($finder as $phpFile) {
            try {
                return $this->findNamespaceFromFile($phpFile->getRealPath());
            } catch (NamespaceNotFoundException $e) {
            }
        }

        throw new NamespaceNotFoundException(sprintf(
            'Cannot find namespace in folder %s',
            $folderPath
        ));
    }

    /**
     * @param string $filePath
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function findNamespaceFromFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'Cannot find namespace from non existent file %s',
                $filePath
            ));
        }

        $phpContent = file_get_contents($filePath);
        if (preg_match(self::NAMESPACE_REGEX, $phpContent, $matches)) {
            return $matches[1];
        }

        throw new NamespaceNotFoundException(sprintf(
            'Cannot find namespace in folder %s',
            $filePath
        ));
    }
}
