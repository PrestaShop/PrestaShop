<?php
/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Preferences;

use PrestaShop\PrestaShop\Adapter\Carrier\HandlingConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Improve > Shipping > Preferences" page.
 */
class PreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var HandlingConfiguration
     */
    private $handlingConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        HandlingConfiguration $handlingConfiguration,
        TranslatorInterface $translator
    ) {
        $this->handlingConfiguration = $handlingConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'handling' => $this->handlingConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        /**
         * All three following fields (shipping_handling_charges, free_shipping_price, free_shipping_weight)
         * can be submitted as either empty or with "0" values
         * and in both cases it means that the functionality is disabled.
         * For consistency changing the values to 0, in case they were submitted empty.
         */
        $data['handling']['shipping_handling_charges'] = $data['handling']['shipping_handling_charges'] ?: 0;
        $data['handling']['free_shipping_price'] = $data['handling']['free_shipping_price'] ?: 0;
        $data['handling']['free_shipping_weight'] = $data['handling']['free_shipping_weight'] ?: 0;

        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return array_merge(
            $this->handlingConfiguration->updateConfiguration($data['handling']),
            [] //@todo second fieldset
        );
    }

    /**
     * Perform validation on form data before saving it
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
                'value' => $data['handling']['shipping_handling_charges'],
                'name' => $this->translator->trans('Handling charges', [], 'Admin.Shopparameters.Feature'),
            ],
            [
                'value' => $data['handling']['free_shipping_price'],
                'name' => $this->translator->trans('Free shipping starts at', [], 'Admin.Shopparameters.Feature'),
            ],
            [
                'value' => $data['handling']['free_shipping_weight'],
                'name' => $this->translator->trans('Free shipping starts at', [], 'Admin.Shopparameters.Feature'),
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
