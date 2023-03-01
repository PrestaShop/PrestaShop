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

namespace Tests\Unit\PrestaShopBundle\License;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\License\AddLicenseOptions;
use PrestaShopBundle\License\AddLicenses;
use PrestaShopBundle\License\LicenseAdder\CSSLicenseAdder;
use PrestaShopBundle\License\LicenseAdder\JSLicenseAdder;
use PrestaShopBundle\License\LicenseAdder\PHPLicenseAdder;
use PrestaShopBundle\License\LicenseAdder\SmartyTemplateLicenseAdder;
use PrestaShopBundle\License\LicenseAdder\TwigLicenseAdder;
use PrestaShopBundle\License\LicenseAdder\VueLicenseAdder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tests\Unit\PrestaShopBundle\License\Specification\AbstractSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\CSSLicenseDeleteDryNotingToDoSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\JSLicenseDeleteDryNotingToDoSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\PHPDeleteDryRunLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\PHPDeleteDryRunOldAndNotAtTheBeginningLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\SmartyTemplateLicenseDeleteDryNotingToDoSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\TwigLicenseDeleteDryNotingToDoSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteAndDryRunOption\VueLicenseDeleteDryNotingToDoSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteOption\PHPDeleteLicenseNotAtTheBeginningLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DeleteOption\PHPDeleteOldLicensesLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\DryRunOption\OSPHPDryRunLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\LicenseSpecificationInterface;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\AFLVueLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\CSSLicenseAlreadyExistsLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\JSLicenseAlreadyExistsLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSCSSLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSJSLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSPHPLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSSmartyTemplateLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSTwigLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\OSVueLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\PHPLicenseAlreadyExistsButNotAtTheBeginningLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\PHPLicenseAlreadyExistsLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\PHPTagIsNotTheFirstElementLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\SmartyTemplateLicenseAlreadyExistsLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\TwigLicenseAlreadyExistsLicenseSpecification;
use Tests\Unit\PrestaShopBundle\License\Specification\NoOption\VueLicenseAlreadyExistsLicenseSpecification;

class AddLicensesTest extends TestCase
{
    /**
     * @dataProvider addOSLicenseTestProvider
     */
    public function testAddOSLicence(LicenseSpecificationInterface $licenseSpecification): void
    {
        //Given
        $licenseSpecification->createTestFiles();

        //When
        $spyAddLicensesLogger = new SpyAddLicensesLogger();
        (new AddLicenses(
                new PHPLicenseAdder(),
                new JSLicenseAdder(),
                new CSSLicenseAdder(),
                new SmartyTemplateLicenseAdder(),
                new TwigLicenseAdder(),
                new VueLicenseAdder()
        ))->execute(
            (new Finder())
                ->in(AbstractSpecification::ACTUAL_FOLDER)
                ->files(),
            $licenseSpecification->getLicenseStrategy(),
            $spyAddLicensesLogger,
            new AddLicenseOptions(
                $licenseSpecification->isDryRun(),
                $licenseSpecification->isDelete(),
                $licenseSpecification->getOldLicensesToRemove()
            )
        );

        //Then
        $this->assertFileEquals($licenseSpecification->getExpectedPathName(), $licenseSpecification->getActualPathName());
        $licenseSpecification->assertLogging($spyAddLicensesLogger);
    }

    public function addOSLicenseTestProvider(): array
    {
        $fs = new Filesystem();

        return [
            [new OSPHPLicenseSpecification($fs)],
            [new OSJSLicenseSpecification($fs)],
            [new OSCSSLicenseSpecification($fs)],
            [new OSSmartyTemplateLicenseSpecification($fs)],
            [new OSTwigLicenseSpecification($fs)],
            [new OSVueLicenseSpecification($fs)],
            [new PHPLicenseAlreadyExistsButNotAtTheBeginningLicenseSpecification($fs)],
            [new PHPLicenseAlreadyExistsLicenseSpecification($fs)],
            [new JSLicenseAlreadyExistsLicenseSpecification($fs)],
            [new CssLicenseAlreadyExistsLicenseSpecification($fs)],
            [new SmartyTemplateLicenseAlreadyExistsLicenseSpecification($fs)],
            [new TwigLicenseAlreadyExistsLicenseSpecification($fs)],
            [new VueLicenseAlreadyExistsLicenseSpecification($fs)],
            [new PHPTagIsNotTheFirstElementLicenseSpecification($fs)],
            //...
            [new AFLVueLicenseSpecification($fs)],
            //...
            //Dry run option  only
            [new OSPHPDryRunLicenseSpecification($fs)],
            //Delete option only
            [new PHPDeleteLicenseNotAtTheBeginningLicenseSpecification($fs)],
            [new PHPDeleteOldLicensesLicenseSpecification($fs)],
            //Dry run + delete
            [new PHPDeleteDryRunLicenseSpecification($fs)],
            [new PHPDeleteDryRunOldAndNotAtTheBeginningLicenseSpecification($fs)],
            [new JSLicenseDeleteDryNotingToDoSpecification($fs)],
            [new VueLicenseDeleteDryNotingToDoSpecification($fs)],
            [new SmartyTemplateLicenseDeleteDryNotingToDoSpecification($fs)],
            [new TwigLicenseDeleteDryNotingToDoSpecification($fs)],
            [new CSSLicenseDeleteDryNotingToDoSpecification($fs)],
        ];
    }

    /**
     * @after
     */
    public function clearTmpFolder(): void
    {
        (new Filesystem())->remove(AbstractSpecification::TMP_FOLDER);
    }
}
