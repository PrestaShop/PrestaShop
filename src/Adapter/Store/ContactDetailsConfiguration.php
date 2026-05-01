<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store;

use Country;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use State;
use Validate;

/**
 * Handles read/write of PS_SHOP_* configuration values for the Contact Details section.
 */
class ContactDetailsConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly Configuration $configuration,
    ) {
    }

    public function getConfiguration(): array
    {
        return [
            'name' => $this->configuration->get('PS_SHOP_NAME'),
            'email' => $this->configuration->get('PS_SHOP_EMAIL'),
            'registration_number' => $this->configuration->get('PS_SHOP_DETAILS'),
            'address1' => $this->configuration->get('PS_SHOP_ADDR1'),
            'address2' => $this->configuration->get('PS_SHOP_ADDR2'),
            'postcode' => $this->configuration->get('PS_SHOP_CODE'),
            'city' => $this->configuration->get('PS_SHOP_CITY'),
            'id_country' => (int) $this->configuration->get('PS_SHOP_COUNTRY_ID') ?: null,
            'id_state' => (int) $this->configuration->get('PS_SHOP_STATE_ID') ?: null,
            'phone' => $this->configuration->get('PS_SHOP_PHONE'),
            'fax' => $this->configuration->get('PS_SHOP_FAX'),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = $this->validateConfiguration($configuration);
        if (!empty($errors)) {
            return $errors;
        }

        $countryId = (int) $configuration['id_country'];
        $stateId = (int) $configuration['id_state'];

        $this->configuration->set('PS_SHOP_NAME', $configuration['name']);
        $this->configuration->set('PS_SHOP_EMAIL', $configuration['email']);
        $this->configuration->set('PS_SHOP_DETAILS', $configuration['registration_number'] ?? '');
        $this->configuration->set('PS_SHOP_ADDR1', $configuration['address1'] ?? '');
        $this->configuration->set('PS_SHOP_ADDR2', $configuration['address2'] ?? '');
        $this->configuration->set('PS_SHOP_CODE', $configuration['postcode'] ?? '');
        $this->configuration->set('PS_SHOP_CITY', $configuration['city'] ?? '');
        $this->configuration->set('PS_SHOP_PHONE', $configuration['phone'] ?? '');
        $this->configuration->set('PS_SHOP_FAX', $configuration['fax'] ?? '');

        if ($countryId) {
            $country = new Country($countryId);
            $this->configuration->set('PS_SHOP_COUNTRY_ID', $countryId);
            $this->configuration->set('PS_SHOP_COUNTRY', $country->iso_code);
        }

        if ($stateId) {
            $state = new State($stateId);
            $this->configuration->set('PS_SHOP_STATE_ID', $stateId);
            $this->configuration->set('PS_SHOP_STATE', $state->iso_code);
        } else {
            $this->configuration->set('PS_SHOP_STATE_ID', 0);
            $this->configuration->set('PS_SHOP_STATE', '');
        }

        return [];
    }

    public function validateConfiguration(array $configuration): array
    {
        $errors = [];

        if (empty($configuration['name'])) {
            $errors[] = [
                'key' => 'The %s field is required.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"' . 'Name' . '"'],
            ];
        } elseif (!Validate::isGenericName($configuration['name'])) {
            $errors[] = [
                'key' => 'The %s field is not valid',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"' . 'Name' . '"'],
            ];
        }

        if (empty($configuration['email'])) {
            $errors[] = [
                'key' => 'The %s field is required.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"' . 'Email address' . '"'],
            ];
        } elseif (!Validate::isEmail($configuration['email'])) {
            $errors[] = [
                'key' => 'The %s field is not valid',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"' . 'Email address' . '"'],
            ];
        }

        $countryId = (int) ($configuration['id_country'] ?? 0);
        $stateId = (int) ($configuration['id_state'] ?? 0);

        if ($countryId && $stateId) {
            $state = new State($stateId);
            if ((int) $state->id_country !== $countryId) {
                $errors[] = [
                    'key' => 'The selected state does not belong to the selected country.',
                    'domain' => 'Admin.Shopparameters.Notification',
                    'parameters' => [],
                ];
            }
        }

        return $errors;
    }
}
