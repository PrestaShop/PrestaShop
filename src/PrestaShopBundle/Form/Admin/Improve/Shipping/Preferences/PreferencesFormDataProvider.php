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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Preferences;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Improve > Shipping > Preferences" page.
 */
class PreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        DataConfigurationInterface $dataConfiguration,
        TranslatorInterface $translator
    ) {
        $this->dataConfiguration = $dataConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->dataConfiguration->updateConfiguration($data);
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
        $numericFields = [
            [
                'value' => $data['shipping_handling_charges'],
                'name' => $this->translator->trans('Handling charges', [], 'Admin.Shipping.Feature'),
            ],
            [
                'value' => $data['free_shipping_price'],
                'name' => $this->translator->trans('Free shipping starts at', [], 'Admin.Shipping.Feature'),
            ],
            [
                'value' => $data['free_shipping_weight'],
                'name' => $this->translator->trans('Free shipping starts at', [], 'Admin.Shipping.Feature'),
            ],
        ];

        // Check if all numeric fields are positive numbers
        foreach ($numericFields as $field) {
            if (!is_numeric($field['value']) || $field['value'] < 0) {
                $errors[] = [
                    'key' => 'The %s field is invalid.',
                    'domain' => 'Admin.Notifications.Error',
                    'parameters' => [$field['name']],
                ];
            }
        }

        return $errors;
    }
}
