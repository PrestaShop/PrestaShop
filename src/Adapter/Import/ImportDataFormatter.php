<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Import;

use Language;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class ImportDataFormatter is an adapter with data formatting methods for import.
 *
 * @deprecated since 9.3, will be removed in the next major version - every method has an
 *             import-engine equivalent, and its remaining callers are the deprecated
 *             \PrestaShop\PrestaShop\Adapter\Import\Handler\* classes:
 *             - getBoolean() -> \PrestaShop\PrestaShop\Core\Import\Engine\ValueParser::parseBoolean()
 *               (which also stops treating "false" and "no" as true)
 *             - getPrice() -> ValueParser::parseDecimal()
 *             - split() -> ValueParser::split() / ValueParser::splitPreservingEmpty()
 *             - createMultiLangField() -> \PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait::localizeForCreation()
 *             - createFriendlyUrl() -> \PrestaShop\PrestaShop\Adapter\Tools::linkRewrite(), which is
 *               all this method ever delegated to
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
     * @param string|int $value
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
        $field = str_replace(
            [',', '%'],
            ['.', ''],
            $field
        );

        return (float) $field;
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
        $result = [];

        foreach (Language::getIDs(false) as $languageId) {
            $result[$languageId] = $field;
        }

        return $result;
    }

    /**
     * Split the field by separator.
     *
     * @param string|null $field
     * @param string $separator
     *
     * @return array
     */
    public function split($field, $separator)
    {
        if (empty($field)) {
            return [];
        }

        if (trim($separator) == '') {
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

        $content = fgetcsv($fd, 0, $separator, '"', '');
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
