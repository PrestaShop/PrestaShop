<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Customer Settings" page.
 */
final class CustomerPreferencesDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $generalDataConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        DataConfigurationInterface $generalDataConfiguration,
        TranslatorInterface $translator
    ) {
        $this->generalDataConfiguration = $generalDataConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'general' => $this->generalDataConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->generalDataConfiguration->updateConfiguration($data['general']);
    }

    /**
     * Perform validations on form data
     *
     * @param array $data
     *
     * @return array    Array of errors if any
     */
    private function validate(array $data)
    {
        $errors = [];

        $passwordResetDelay = $data['general']['password_reset_delay'];
        if (!is_numeric($passwordResetDelay) || $passwordResetDelay < 0) {
            $fieldName = $this->translator->trans('Password reset delay', [], 'Admin.Shopparameters.Feature');

            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$fieldName],
            ];
        }

        return $errors;
    }
}
