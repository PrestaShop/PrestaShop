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

use Language;
use PHPUnit\Framework\Assert;
use ProductAttribute;
use RuntimeException;

class AttributeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given attribute :attributeReference named :name in :langIso language exists
     *
     * @param string $attributeReference
     * @param string $name
     * @param string $langIso
     */
    public function assertNamedAttributeExists(string $attributeReference, string $name, string $langIso): void
    {
        $langId = (int) Language::getIdByIso($langIso);

        if (!$langId) {
            throw new RuntimeException(sprintf('Language by iso code "%s" was not found', $langIso));
        }

        $attributes = ProductAttribute::getAttributes($langId);
        $foundAttributeId = null;

        foreach ($attributes as $attribute) {
            if ($attribute['name'] === $name) {
                $foundAttributeId = (int) $attribute['id_attribute'];

                break;
            }
        }

        Assert::assertNotNull($foundAttributeId, sprintf('Attribute named "%s" was not found', $name));
        $this->getSharedStorage()->set($attributeReference, $foundAttributeId);
    }
}
