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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderPreferences;

use PrestaShop\PrestaShop\Adapter\CMS\CMSDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Order Settings" page.
 */
class OrderPreferencesGeneralFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

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
        TranslatorInterface $translator,
        CMSDataProvider $cmsDataProvider
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->translator = $translator;
        $this->cmsDataProvider = $cmsDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->generalConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // If TOS option is disabled - reset the cms id as well
        if (!$data['enable_tos']) {
            $data['tos_cms_id'] = 0;
        }

        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->generalConfiguration->updateConfiguration($data);
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
        $purchaseMinimumValue = $data['purchase_minimum_value'] ?? null;

        // Check if purchase minimum value is a positive number
        if (!is_numeric($purchaseMinimumValue) || $purchaseMinimumValue < 0) {
            $errors[] = $this->translator->trans(
                'Minimum purchase total required in order to validate the order',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        // If TOS option is enabled - check if the selected CMS page is valid
        if ($data['enable_tos']) {
            $tosCmsId = $data['tos_cms_id'];
            $tosCms = $this->cmsDataProvider->getCMSById($tosCmsId);

            if (!$tosCms->id) {
                $errors[] = [
                    'key' => 'Assign a valid page if you want it to be read.',
                    'domain' => 'Admin.Shopparameters.Notification',
                    'parameters' => [],
                ];
            }
        }

        return $errors;
    }
}
