<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DOMDocument;
use SimpleXMLElement;

/**
 * This script will update the tax rule groups for virtual products from all EU localization packs.
 * All it needs is that the correct tax in each localization pack is marked with `eu-tax-group="virtual"`.
 **.
 *
 * 1)
 *  Parse all files under /localization,
 *  looking for <tax> elements that have the attribute eu-tax-group="virtual".
 *
 *  Store the list of files (`$euLocalizationFiles`) where such taxes have been found,
 *  in a next step we'll store the new tax group in each of them.
 *
 * 2)
 * Remove all taxRulesGroup's that have the attribute eu-tax-group="virtual".
 *
 * 3)
 * Build a new taxRulesGroup containing all the taxes found in the first step.
 *
 * 4)
 * Inject the new taxRulesGroup into all packs of `$euLocalizationFiles`, not forgetting
 * to also inject the required taxes.
 *
 * Warning: do not duplicate the tax with attribute eu-tax-group="virtual" of the pack being updated.
 *
 * Mark the injected group with the attributes eu-tax-group="virtual" and auto-generated="1"
 * Mark the injected taxes witth the attributes from-eu-tax-group="virtual" and auto-generated="1"
 *
 * Clean things up by removing all the previous taxes that had the attributes eu-tax-group="virtual" and auto-generated="1"
 */
class UpdateEUTaxruleGroupsCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this
            ->setName('prestashop:taxes:update-eu-tax-rule-groups')
            ->setDescription('Update EU Tax rule groups');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /* Tweak */
        $this->output = $output;

        $localizationPacksRoot = $this->getContainer()->getParameter('kernel.root_dir') . '/../localization';

        if (!$localizationPacksRoot) {
            return $output->writeln("<error>Could not find the folder containing the localization files (should be 'localization' at the root of the PrestaShop folder)</error>");
        }

        $euLocalizationFiles = array();

        foreach (scandir($localizationPacksRoot, SCANDIR_SORT_NONE) as $entry) {
            if (!preg_match('/\.xml$/', $entry)) {
                continue;
            }

            $localizationPackFile = $localizationPacksRoot . DIRECTORY_SEPARATOR . $entry;

            $localizationPack = @simplexml_load_file($localizationPackFile);

            // Some packs do not have taxes
            if (!$localizationPack || !$localizationPack->taxes->tax) {
                continue;
            }

            foreach ($localizationPack->taxes->tax as $tax) {
                if ((string) $tax['eu-tax-group'] === 'virtual') {
                    if (!isset($euLocalizationFiles[$localizationPackFile])) {
                        $euLocalizationFiles[$localizationPackFile] = array(
                            'virtualTax' => $tax,
                            'pack' => $localizationPack,
                            'iso_code_country' => basename($entry, '.xml'),
                        );
                    } else {
                        return $output->writeln("<error>Too many taxes with eu-tax-group=\"virtual\" found in `$localizationPackFile`.");
                    }
                }
            }
        }

        foreach ($euLocalizationFiles as $path => $file) {
            $nodesToKill = array();

            // Get max tax id, and list of nodes to kill
            $taxId = 0;
            foreach ($file['pack']->taxes->tax as $tax) {
                if ((string) $tax['auto-generated'] === '1' && (string) $tax['from-eu-tax-group'] === 'virtual') {
                    $nodesToKill[] = $tax;
                } else {
                    // We only count the ids of the taxes we're not going to remove!
                    $taxId = max($taxId, (int) $tax['id']);
                }
            }

            foreach ($file['pack']->taxes->taxRulesGroup as $trg) {
                if ((string) $trg['auto-generated'] === '1' && (string) $trg['eu-tax-group'] === 'virtual') {
                    $nodesToKill[] = $trg;
                }
            }

            // This is the first tax id we're allowed to use.
            ++$taxId;

            // Prepare new taxRulesGroup

            $taxRulesGroup = $file['pack']->taxes->addChild('taxRulesGroup');
            $taxRulesGroup->addAttribute('name', 'EU VAT For Virtual Products');
            $taxRulesGroup->addAttribute('auto-generated', '1');
            $taxRulesGroup->addAttribute('eu-tax-group', 'virtual');

            $this->addTaxRule($taxRulesGroup, $file['virtualTax'], $file['iso_code_country']);

            foreach ($euLocalizationFiles as $foreignPath => $foreignFile) {
                if ($foreignPath === $path) {
                    // We already added the tax that belongs to this pack
                    continue;
                }

                $tax = $this->addTax($file['pack']->taxes, $foreignFile['virtualTax'], array(
                    'id' => (string) $taxId,
                    'auto-generated' => '1',
                    'from-eu-tax-group' => 'virtual',
                ), array('eu-tax-group'));

                $this->addTaxRule($taxRulesGroup, $tax, $foreignFile['iso_code_country']);

                ++$taxId;
            }

            foreach ($nodesToKill as $node) {
                unset($node[0]);
            }

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($file['pack']->asXML());
            file_put_contents($path, $dom->saveXML());
        }

        $nUpdated = count($euLocalizationFiles);

        $output->writeln("<info>Updated the virtual tax groups for $nUpdated localization files</info>");
    }

    protected function addTax(\SimpleXMLElement $taxes, \SimpleXMLElement $tax, array $attributesToUpdate = array(), array $attributesToRemove = array())
    {
        $newTax = new SimpleXMLElement('<tax/>');

        $taxRulesGroups = $taxes->xpath('//taxRulesGroup[1]');
        $insertBefore = $taxRulesGroups[0];

        if (!$insertBefore) {
            return $this->output->writeln("<error>Could not find any `taxRulesGroup`, don't know where to append the tax.");
        }

        /**
         * Add the `tax` node before the first `taxRulesGroup`.
         * Yes, the dom API is beautiful.
         */
        $dom = dom_import_simplexml($taxes);

        $new = $dom->insertBefore(
            $dom->ownerDocument->importNode(dom_import_simplexml($newTax)),
            dom_import_simplexml($insertBefore)
        );

        $newTax = simplexml_import_dom($new);

        $newAttributes = array();

        foreach ($tax->attributes() as $attribute) {
            $name = $attribute->getName();

            // This attribute seems to cause trouble, skip it.
            if ($name === 'account_number' || in_array($name, $attributesToRemove)) {
                continue;
            }

            $value = (string) $attribute;

            $newAttributes[$name] = $value;
        }

        $newAttributes = array_merge($newAttributes, $attributesToUpdate);

        foreach ($newAttributes as $name => $value) {
            $newTax->addAttribute($name, $value);
        }

        return $newTax;
    }

    protected function addTaxRule(\SimpleXMLElement $taxRulesGroup, \SimpleXMLElement $tax, $iso_code_country)
    {
        $taxRule = $taxRulesGroup->addChild('taxRule');

        $taxRule->addAttribute('iso_code_country', $iso_code_country);
        $taxRule->addAttribute('id_tax', (string) $tax['id']);

        return $taxRule;
    }
}
