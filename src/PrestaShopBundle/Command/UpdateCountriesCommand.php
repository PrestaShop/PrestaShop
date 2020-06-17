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

namespace PrestaShopBundle\Command;

use DOMDocument;
use Exception;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class UpdateCountriesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:countries:update')
            ->setDescription('Update countries files for the installer')
            ->addArgument('installFolder', InputArgument::REQUIRED, 'Install folder');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $reader = $container->get('prestashop.core.localization.cldr.reader');

        $template = $input->getArgument('installFolder') . DIRECTORY_SEPARATOR . 'langs' . DIRECTORY_SEPARATOR . 'en'
            . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'country.xml';
        if (!is_file($template)) {
            throw new Exception(sprintf('File "%s" not found', $template));
        }
        $xmlTemplate = new SimpleXMLElement(file_get_contents($template));

        $finder = new Finder();
        $finder
            ->directories()
            ->depth(0)
            ->in($input->getArgument('installFolder') . DIRECTORY_SEPARATOR . 'langs')
            ->exclude('en');

        $output->writeln('Updating countries translations in ' . count($finder) . ' directories...');
        $progress = new ProgressBar($output, count($finder));
        $progress->start();

        foreach ($finder as $directory) {
            $locale = $directory->getFilename();
            $localeData = $reader->readLocaleData($locale);
            $territories = $localeData->getTerritories();
            if (empty($territories)) {
                $progress->advance();
                continue;
            }
            $outputFile = $input->getArgument('installFolder') . DIRECTORY_SEPARATOR . 'langs' . DIRECTORY_SEPARATOR .
                $locale . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'country.xml';

            // Directory
            if (!is_dir(dirname($outputFile))) {
                mkdir(dirname($outputFile));
            }
            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput = true;
            $xmlRoot = $xml->createElement('entity_country');
            foreach ($xmlTemplate->country as $country) {
                $localeID = (string) $country->attributes()->id;

                $xmlCountry = $xml->createElement('country');
                $xmlCountryID = $xml->createAttribute('id');
                $xmlCountryID->value = $localeID;
                $xmlCountryID = $xmlCountry->appendChild($xmlCountryID);

                $xmlName = $xml->createElement('name');
                $xmlName = $xmlCountry->appendChild($xmlName);

                $xmlText = $xml->createTextNode($territories[$localeID] ?? (string) $country->name);
                $xmlText = $xmlName->appendChild($xmlText);

                $xmlRoot->appendChild($xmlCountry);
            }
            $xml->appendChild($xmlRoot);
            $xml->save($outputFile);
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
    }
}
