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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderPreferences;

use PrestaShop\PrestaShop\Adapter\CMS\CMSDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\GeneralConfiguration;
use PrestaShop\PrestaShop\Adapter\Order\GiftOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Order Settings" page.
 *
 * @deprecated since 1.7.8, will be removed in the next major version
 */
class OrderPreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

    /**
     * @var GiftOptionsConfiguration
     */
    private $giftOptionsConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CMSDataProvider
     */
    private $cmsDataProvider;

    public function __construct(
        GeneralConfiguration $generalConfiguration,
        GiftOptionsConfiguration $giftOptionsConfiguration,
        TranslatorInterface $translator,
        CMSDataProvider $cmsDataProvider
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->giftOptionsConfiguration = $giftOptionsConfiguration;
        $this->translator = $translator;
        $this->cmsDataProvider = $cmsDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'general' => $this->generalConfiguration->getConfiguration(),
            'gift_options' => $this->giftOptionsConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // If TOS option is disabled - reset the cms id as well
        if (!$data['general']['enable_tos']) {
            $data['general']['tos_cms_id'] = 0;
        }

        // If gift wrapping tax rules group was not submitted - reset it to 0
        if (!isset($data['gift_options']['gift_wrapping_tax_rules_group'])) {
            $data['gift_options']['gift_wrapping_tax_rules_group'] = 0;
        }

        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return array_merge(
            $this->generalConfiguration->updateConfiguration($data['general']),
            $this->giftOptionsConfiguration->updateConfiguration($data['gift_options'])
        );
    }

    /**
     * Perform validation on form data before saving it.
     *
     * @param array $data
     *
     * @return array Returns array of errors
     */
    protected function validate(array $data)
    {
        $errors = [];
        $invalidFields = [];
        $purchaseMinimumValue = $data['general']['purchase_minimum_value'];
        $giftWrappingPrice = $data['gift_options']['gift_wrapping_price'];

        // Check if purchase minimum value is a positive number
        if (!is_numeric($purchaseMinimumValue) || $purchaseMinimumValue < 0) {
            $invalidFields[] = $this->translator->trans(
                'Minimum purchase total required in order to validate the order',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $isTosEnabled = $data['general']['enable_tos'];

        // If TOS option is enabled - check if the selected CMS page is valid
        if ($isTosEnabled) {
            $tosCmsId = $data['general']['tos_cms_id'];
            $tosCms = $this->cmsDataProvider->getCMSById($tosCmsId);

            if (!$tosCms->id) {
                $errors[] = [
                    'key' => 'Assign a valid page if you want it to be read.',
                    'domain' => 'Admin.Shopparameters.Notification',
                    'parameters' => [],
                ];
            }
        }

        // Check if purchase minimum value is a positive number
        if (!empty($giftWrappingPrice) && (!is_numeric($giftWrappingPrice) || $giftWrappingPrice < 0)) {
            $invalidFields[] = $this->translator->trans(
                'Gift-wrapping price',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        foreach ($invalidFields as $invalidField) {
            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$invalidField],
            ];
        }

        return $errors;
    }
}
