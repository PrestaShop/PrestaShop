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

use AttributeGroup;
use Language;
use PHPUnit\Framework\Assert;
use RuntimeException;

class AttributeGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given attribute group :reference named :name in :langIso language exists
     *
     * @param string $attributeGroupReference
     * @param string $name
     * @param string $langIso
     */
    public function assertNamedAttributeGroupExists(string $attributeGroupReference, string $name, string $langIso): void
    {
        $langId = (int) Language::getIdByIso($langIso);

        if (!$langId) {
            throw new RuntimeException(sprintf('Language by iso code "%s" was not found', $langIso));
        }

        $attributeGroups = AttributeGroup::getAttributesGroups($langId);
        $foundGroupId = null;

        foreach ($attributeGroups as $attributeGroup) {
            if ($attributeGroup['name'] === $name) {
                $foundGroupId = (int) $attributeGroup['id_attribute_group'];

                break;
            }
        }

        Assert::assertNotNull($foundGroupId, sprintf('Attribute group named "%s" was not found', $name));
        $this->getSharedStorage()->set($attributeGroupReference, $foundGroupId);
    }
}
