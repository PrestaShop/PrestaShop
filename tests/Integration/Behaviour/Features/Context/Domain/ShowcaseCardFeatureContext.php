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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ShowcaseCardFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When employee :employee closes showcase card :cardName
     *
     * @param string $cardName
     * @param string $employeeReference
     */
    public function employeeClosesShowcaseCard(string $cardName, string $employeeReference)
    {
        $employeeId = SharedStorage::getStorage()->get($employeeReference);
        $this->getCommandBus()->handle(new CloseShowcaseCardCommand($employeeId, $cardName));
    }

    /**
     * @Then employee :employeeReference should not see showcase card :cardName
     *
     * @param string $employeeReference
     * @param string $cardName
     */
    public function employeeShouldNotSeeShowcaseCard(string $employeeReference, string $cardName)
    {
        $employeeId = SharedStorage::getStorage()->get($employeeReference);

        $showcaseCardIsClosed = $this->getQueryBus()->handle(
            new GetShowcaseCardIsClosed((int) $employeeId, $cardName)
        );
        Assert::assertTrue($showcaseCardIsClosed);
    }
}
