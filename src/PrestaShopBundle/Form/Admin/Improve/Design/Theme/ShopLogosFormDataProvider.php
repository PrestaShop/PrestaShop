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

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Form\DTO\ShopRestriction;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\MultiStoreSettingsFormDataProviderInterface;

/**
 * {@inheritdoc}
 */
final class ShopLogosFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var MultiStoreSettingsFormDataProviderInterface
     */
    private $themeMultiStoreSettingsFormDataProvider;

    /**
     * @param CommandBusInterface $commandBus
     * @param MultiStoreSettingsFormDataProviderInterface $themeMultiStoreSettingsFormDataProvider
     */
    public function __construct(
        CommandBusInterface $commandBus,
        MultiStoreSettingsFormDataProviderInterface $themeMultiStoreSettingsFormDataProvider
    ) {
        $this->commandBus = $commandBus;
        $this->themeMultiStoreSettingsFormDataProvider = $themeMultiStoreSettingsFormDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->themeMultiStoreSettingsFormDataProvider->getData();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function setData(array $data)
    {
        $data = $this->geFilteredFieldsByShopRestriction($data);

        $command = new UploadLogosCommand();

        if (!empty($data['header_logo'])) {
            $command->setUploadedHeaderLogo($data['header_logo']);
        }

        if (!empty($data['mail_logo'])) {
            $command->setUploadedMailLogo($data['mail_logo']);
        }

        if (!empty($data['invoice_logo'])) {
            $command->setUploadedInvoiceLogo($data['invoice_logo']);
        }

        if (!empty($data['favicon'])) {
            $command->setUploadedFavicon($data['favicon']);
        }

        $this->commandBus->handle($command);

        return [];
    }

    /**
     * If shop_restriction argument exists in the post this means that certain shop restrictions are applied.
     * It filters and drops the values which are not being selected for editing for specific shop.
     *
     * @param array $data - form data
     *
     * @return array
     */
    private function geFilteredFieldsByShopRestriction(array $data)
    {
        if (!isset($data['shop_restriction'])) {
            return $data;
        }

        /** @var ShopRestriction $shopRestriction */
        $shopRestriction = $data['shop_restriction'];

        $shopRestrictionFields = $shopRestriction->getShopRestrictionFields();

        foreach ($shopRestrictionFields as $shopRestrictionField) {
            $doesValueExistsAndNotRestrictedToShop = isset($data[$shopRestrictionField->getFieldName()]) &&
                !$shopRestrictionField->isRestrictedToContextShop();

            if ($doesValueExistsAndNotRestrictedToShop) {
                unset($data[$shopRestrictionField->getFieldName()]);
            }
        }

        return $data;
    }
}
