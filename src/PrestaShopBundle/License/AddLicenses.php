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

declare(strict_types=1);

namespace PrestaShopBundle\License;

use PrestaShopBundle\License\LicenseAdder\LicenseAdderInterface;
use PrestaShopBundle\License\LicenseBuilder\LicenseStrategyInterface;
use Symfony\Component\Finder\Finder;

class AddLicenses implements AddLicensesInterface
{
    private $licenseAdders;

    public function __construct(LicenseAdderInterface ...$licenseAdders)
    {
        $this->licenseAdders = $licenseAdders;
    }

    public function execute(
        Finder $finder,
        LicenseStrategyInterface $licenseStrategy,
        AddLicensesLoggerInterface $addLicensesLogger,
        AddLicenseOptions $options
    ): void {
        foreach ($this->licenseAdders as $licenseAdder) {
            $finderWithExtension = clone $finder;
            $finderWithExtension->name('*.' . $licenseAdder->getExtensionSupported());
            $addLicensesLogger->startExtension($licenseAdder->getExtensionSupported(), count($finderWithExtension));

            foreach ($finderWithExtension as $file) {
                $addLicensesLogger->progress();
                $contents = $file->getContents();
                if ($options->isDelete()) {
                    $oldContents = $contents;
                    $contents = $licenseAdder->removeLicencesNotAtTheBeginning($licenseStrategy, $contents);
                    if ($oldContents !== $contents) {
                        if ($options->isDryRun()) {
                            $addLicensesLogger->logDryCurrentLicenseDeletions($file);
                        } else {
                            $addLicensesLogger->logCurrentLicenseDeletions($file);
                        }
                    }

                    $oldContents = $contents;
                    foreach ($options->getOldLicensesToRemove() as $oldLicense) {
                        $contents = str_replace($oldLicense, '', $contents);
                    }
                    if ($oldContents !== $contents) {
                        if ($options->isDryRun()) {
                            $addLicensesLogger->logDryOldLicenseDeletions($file);
                        } else {
                            $addLicensesLogger->logOldLicenseDeletions($file);
                        }
                    }
                }

                if ($licenseAdder->hasLicense($licenseStrategy, $contents)) {
                    continue;
                }

                if ($options->isDryRun()) {
                    $addLicensesLogger->logDryInsertion($file);
                    continue;
                }

                file_put_contents(
                    $file->getPathname(),
                    $licenseAdder->addLicenceToContent($licenseStrategy, $contents)
                );
                $addLicensesLogger->logInsertion($file);
            }

            $addLicensesLogger->finishExtension($licenseAdder->getExtensionSupported());
        }
    }
}
