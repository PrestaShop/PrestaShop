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

namespace Tests\Unit\PrestaShopBundle\License\Specification\NoOption;

use PHPUnit\Framework\Assert;
use PrestaShopBundle\License\LicenseBuilder\LicenseStrategyInterface;
use PrestaShopBundle\License\LicenseBuilder\OSLicenseStrategy;
use Tests\Unit\PrestaShopBundle\License\SpyAddLicensesLogger;

class VueLicenseAlreadyExistsLicenseSpecification extends AbstractNoOptionSpecification
{
    protected function getActualContent(): string
    {
        return '<!--**
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
 *-->
<h1>Hello world</h1>';
    }

    protected function getExpectedContent(): string
    {
        return '<!--**
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
 *-->
<h1>Hello world</h1>';
    }

    protected function getExtension(): string
    {
        return 'vue';
    }

    public function assertLogging(SpyAddLicensesLogger $spyAddLicensesLogger): void
    {
        Assert::assertEquals(1, $spyAddLicensesLogger->getStartExtensionCount($this->getExtension()));
        Assert::assertEquals(1, $spyAddLicensesLogger->getFinishExtensionCount($this->getExtension()));
        Assert::assertEquals(1, $spyAddLicensesLogger->getProgressCount());
        Assert::assertEquals(0, $spyAddLicensesLogger->getInsertCount());
        Assert::assertEquals(0, $spyAddLicensesLogger->getCurrentDeletionCount());
        Assert::assertEquals(0, $spyAddLicensesLogger->getDryCurrentDeletionCount());
        Assert::assertEquals(0, $spyAddLicensesLogger->getDryOldDeletionCount());
        Assert::assertEquals(0, $spyAddLicensesLogger->getOldDeletionCount());
    }

    public function getLicenseStrategy(): LicenseStrategyInterface
    {
        return new OSLicenseStrategy();
    }

    public function getOldLicensesToRemove(): array
    {
        return [];
    }
}
