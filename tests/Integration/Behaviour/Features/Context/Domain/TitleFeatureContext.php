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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\Title\Command\BulkDeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\DeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleNotFoundException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class TitleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I delete the title :titleReference
     *
     * @param string $titleReference
     */
    public function deleteTitle(string $titleReference): void
    {
        /** @var int $titleId */
        $titleId = SharedStorage::getStorage()->get($titleReference);

        try {
            $this->getCommandBus()->handle(new DeleteTitleCommand((int) $titleId));
        } catch (TitleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete titles :titleReferences using bulk action
     *
     * @param string $titleReferences
     */
    public function bulkDeleteTitles(string $titleReferences): void
    {
        $titleIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($titleReferences) as $titleReference) {
            $titleIds[] = (int) SharedStorage::getStorage()->get($titleReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteTitleCommand($titleIds));
        } catch (TitleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that the title has not been found
     */
    public function assertLastErrorTitleNotFound(): void
    {
        $this->assertLastErrorIs(TitleNotFoundException::class);
    }
}
