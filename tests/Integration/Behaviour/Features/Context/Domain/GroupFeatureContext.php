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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Group;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use RuntimeException;

class GroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer representing group id which should never exist in test database
     */
    public const NON_EXISTING_GROUP_ID = 74011211;

    /**
     * @Given group :groupReference named :name exists
     *
     * @param string $groupReference
     * @param string $name
     */
    public function assertGroupExists(string $groupReference, string $name): void
    {
        $group = Group::searchByName($name);
        if (!$group) {
            throw new RuntimeException(sprintf('Group "%s" does not exist', $groupReference));
        }

        $this->getSharedStorage()->set($groupReference, (int) $group['id_group']);
    }

    /**
     * @Given group :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingGroupReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that group "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_GROUP_ID);
    }

    /**
     * @Then I should get error that group was not found
     */
    public function assertGroupNotFound(): void
    {
        $this->assertLastErrorIs(GroupNotFoundException::class);
    }
}
