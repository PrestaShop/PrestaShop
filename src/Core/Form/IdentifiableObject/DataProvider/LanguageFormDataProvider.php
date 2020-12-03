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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;

/**
 * Provides data for language's add/edit forms
 */
final class LanguageFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $bus
     * @param bool $isMultistoreFeatureActive
     * @param int[] $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $bus,
        $isMultistoreFeatureActive,
        array $defaultShopAssociation
    ) {
        $this->bus = $bus;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($languageId)
    {
        /** @var EditableLanguage $editableLanguage */
        $editableLanguage = $this->bus->handle(new GetLanguageForEditing($languageId));

        $data = [
            'name' => $editableLanguage->getName(),
            'iso_code' => $editableLanguage->getIsoCode()->getValue(),
            'tag_ietf' => $editableLanguage->getTagIETF()->getValue(),
            'short_date_format' => $editableLanguage->getShortDateFormat(),
            'full_date_format' => $editableLanguage->getFullDateFormat(),
            'is_rtl' => $editableLanguage->isRtl(),
            'is_active' => $editableLanguage->isActive(),
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $editableLanguage->getShopAssociation();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'short_date_format' => 'Y-m-d',
            'full_date_format' => 'Y-m-d H:i:s',
            'is_rtl' => false,
            'is_active' => true,
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
