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

namespace PrestaShop\PrestaShop\Adapter\Import;

use Language;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class ImportDataFormatter is an adapter with data formatting methods for import.
 */
final class ImportDataFormatter
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Tools
     */
    private $tools;

    public function __construct(
        ConfigurationInterface $configuration,
        Tools $tools
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function getBoolean($value)
    {
        return (bool) $value;
    }

    /**
     * @param string $field
     *
     * @return float
     */
    public function getPrice($field)
    {
        $field = (float) str_replace(',', '.', $field);
        $field = (float) str_replace('%', '', $field);

        return $field;
    }

    /**
     * Create a multilang field.
     *
     * @param string $field
     *
     * @return array
     */
    public function createMultiLangField($field)
    {
        $result = array();

        foreach (Language::getIDs(false) as $languageId) {
            $result[$languageId] = $field;
        }

        return $result;
    }

    /**
     * Split the field by separator.
     *
     * @param string $field
     * @param string $separator
     *
     * @return array
     */
    public function split($field, $separator)
    {
        if (empty($field)) {
            return [];
        }

        if (is_null($separator) || trim($separator) == '') {
            $separator = ',';
        }

        $uniqidPath = false;

        // try data:// protocol. If failed, old school file on filesystem.
        if (false === ($fd = @fopen('data://text/plain;base64,' . base64_encode($field), 'rb'))) {
            do {
                $uniqidPath = $this->configuration->get('_PS_UPLOAD_DIR_') . uniqid();
            } while (file_exists($uniqidPath));
            file_put_contents($uniqidPath, $field);
            $fd = fopen($uniqidPath, 'r');
        }

        if ($fd === false) {
            return [];
        }

        $content = fgetcsv($fd, 0, $separator);
        fclose($fd);

        if ($uniqidPath !== false && file_exists($uniqidPath)) {
            @unlink($uniqidPath);
        }

        if (empty($content) || !is_array($content)) {
            return [];
        }

        return $content;
    }

    /**
     * Transform given value into a friendly url string.
     *
     * @param string $value
     *
     * @return string
     */
    public function createFriendlyUrl($value)
    {
        return $this->tools->linkRewrite($value);
    }
}
