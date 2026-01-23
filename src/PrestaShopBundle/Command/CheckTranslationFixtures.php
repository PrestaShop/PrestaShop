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

namespace PrestaShopBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CheckTranslationFixtures extends Command
{
    protected const DATA_BASE_FILE = 'install-dev/langs/en/data';

    protected const DATA_FIXTURE_FILE = 'install-dev/fixtures/fashion/langs/en/data';

    protected const LANG_KEYS = 'classes/lang/KeysReference/%sLang.php';

    protected const LANG_FILE = 'classes/lang/%sLang.php';

    protected const LANG_CLASS = '%sLangCore';

    protected const OBJECT_FILE = 'classes/%s.php';

    protected function configure()
    {
        $this
            ->setName('prestashop:translation:check-fixtures')
            ->setDescription('Check fixtures of your translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch files from directories
        $finder = new Finder();
        $finderFiles = $finder->files()
            ->in([self::DATA_BASE_FILE, self::DATA_FIXTURE_FILE])
            ->name('*.xml');
        $files = [];
        foreach ($finderFiles as $k => $file) {
            $files[] = $file->getFilename();
        }
        $files = array_unique($files);
        sort($files);

        foreach ($files as $inputFile) {
            // Ex: attribute_group.xml
            $objectClass = str_replace('.xml', '', $inputFile);
            $objectClass = str_replace('_', ' ', $objectClass);
            $objectClass = ucwords($objectClass);
            // Ex: AttributeGroup
            $objectClass = str_replace(' ', '', $objectClass);

            // Ex: classes/lang/KeysReference/AttributeGroupLang.php
            $outputFile = sprintf(self::LANG_KEYS, $objectClass);
            // Ex: classes/lang/AttributeGroupLang.php
            $langFile = sprintf(self::LANG_FILE, $objectClass);
            // Ex: AttributeGroupLangCore
            $langClass = sprintf(self::LANG_CLASS, $objectClass);
            // Ex: classes/AttributeGroup.php
            $objectFile = sprintf(self::OBJECT_FILE, $objectClass);

            $output->writeLn('Input File : ' . $inputFile);
            if (!file_exists($outputFile)) {
                $output->writeLn('<comment>> OutputFile : ' . $outputFile . ' is not existing</comment>');
                $output->writeLn('');
                continue;
            }
            $output->writeLn('> Output File : ' . $outputFile);
            if (!file_exists($langFile)) {
                $output->writeLn('<comment>> LangFile : ' . $langFile . ' is not existing</comment>');
                $output->writeLn('');
                continue;
            }
            $output->writeLn('> LangFile : ' . $langFile);
            include_once $langFile;

            $langInstance = new $langClass('');

            $fields = $langInstance->getFieldsToUpdate();
            if (!in_array($inputFile, ['configuration.xml']) && file_exists($objectFile)) {
                include_once $objectFile;
                // Remove all fields not translatable
                foreach ($fields as $kField => $field) {
                    if (!isset($objectClass::$definition['fields'][$field]['lang'])
                        || $objectClass::$definition['fields'][$field] === false) {
                        unset($fields[$kField]);
                    }
                }
            }
            $langValues = [];

            // Base File
            $outputContent = $this->getBaseContent();
            foreach ([self::DATA_BASE_FILE, self::DATA_FIXTURE_FILE] as $dir) {
                $xmlFile = $dir . '/' . $inputFile;
                if (!file_exists($xmlFile)) {
                    continue;
                }
                $xml = simplexml_load_file($xmlFile);

                $outputContent .= '// ' . $xmlFile . PHP_EOL;
                foreach ($xml as $xmlItem) {
                    foreach ($fields as $field) {
                        $value = !empty($xmlItem->{$field}) ? (string) $xmlItem->{$field} : '';
                        $value = $value === '' ? (string) $xmlItem->attributes()->$field : $value;
                        $value = str_replace(chr(13), '', $value);
                        $value = str_replace(chr(10), '\n', $value);
                        if ($value === '' || in_array($value, $langValues)) {
                            continue;
                        }
                        $langValues[] = $value;
                        $outputContent .= sprintf(
                            'trans(\'%s\', \'%s\');' . PHP_EOL,
                            $value,
                            $langInstance->getDomain()
                        );
                    }
                }
            }

            unset($langObject);

            $output->writeLn('<info>> Output File written</info>');

            file_put_contents($outputFile, $outputContent);

            $output->writeLn('');
        }

        return 0;
    }

    protected function getBaseContent(): string
    {
        return '<?php
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
 */' . PHP_EOL;
    }
}
