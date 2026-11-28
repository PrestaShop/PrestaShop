<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store;

use Country;
use Exception;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use State;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Validate;

final class ContactDetailsConfiguration extends AbstractMultistoreConfiguration
{
    private const TEXT_FIELD_KEYS = [
        'name' => 'PS_SHOP_NAME',
        'email' => 'PS_SHOP_EMAIL',
        'registration_number' => 'PS_SHOP_DETAILS',
        'address1' => 'PS_SHOP_ADDR1',
        'address2' => 'PS_SHOP_ADDR2',
        'postcode' => 'PS_SHOP_CODE',
        'city' => 'PS_SHOP_CITY',
        'phone' => 'PS_SHOP_PHONE',
        'fax' => 'PS_SHOP_FAX',
    ];

    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();
        $configuration = [];
        foreach (self::TEXT_FIELD_KEYS as $field => $key) {
            $configuration[$field] = $this->configuration->get($key, null, $shopConstraint);
        }
        $configuration['id_country'] = (int) $this->configuration->get('PS_SHOP_COUNTRY_ID', null, $shopConstraint) ?: null;
        $configuration['id_state'] = (int) $this->configuration->get('PS_SHOP_STATE_ID', null, $shopConstraint) ?: null;

        return $configuration;
    }

    /**
     * @throws Exception
     */
    public function updateConfiguration(array $configuration): array
    {
        $countryId = (int) ($configuration['id_country'] ?? 0);
        $stateId = (int) ($configuration['id_state'] ?? 0);

        // Country names are multilingual: legacy loaded the country with a language id before
        // storing PS_SHOP_COUNTRY, so loading it without one would store an array here.
        $langId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $country = $countryId ? new Country($countryId, $langId) : null;
        $state = ($stateId && $countryId) ? new State($stateId) : null;

        // A Country overridden for the shop alone keeps the State inherited from all shops, which may belong to
        // another country: the State is then overridden along with it, emptied.
        if ($state !== null && (int) $state->id_country !== $countryId
            && $this->isOverriddenForShop($configuration, 'id_country')
            && !$this->isOverriddenForShop($configuration, 'id_state')
        ) {
            $configuration[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'id_state'] = true;
            $state = null;
        }

        // A shop removing its Country override inherits the Country again, while the disabled field still submits
        // its own one. Its State override is kept only if it belongs to the inherited Country: an empty one (whose
        // row is hidden with a country without states) or one of another country is removed with it.
        if ($this->isOverrideRemovedForShop($configuration, 'id_country', 'PS_SHOP_COUNTRY_ID')) {
            $countryId = $this->getInheritedCountryId();
            $country = $countryId ? new Country($countryId, $langId) : null;
            if ($this->isOverriddenForShop($configuration, 'id_state')
                && ($state === null || (int) $state->id_country !== $countryId)
            ) {
                unset($configuration[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'id_state']);
                $state = null;
            }
        }

        $errors = $this->collectValidationErrors($configuration, $country, $state, $countryId);
        if (!empty($errors)) {
            return $errors;
        }

        $shopConstraint = $this->getShopConstraint();
        foreach (self::TEXT_FIELD_KEYS as $field => $key) {
            $this->updateConfigurationValue($key, $field, [$field => $configuration[$field] ?? ''] + $configuration, $shopConstraint);
        }

        // The name keys follow the override checkbox of their id field, which is the only one the form shows.
        $this->updateConfigurationValue('PS_SHOP_COUNTRY_ID', 'id_country', ['id_country' => $country !== null ? $countryId : 0] + $configuration, $shopConstraint);
        // The name, not the ISO code: legacy stored Country::$name here and this value is
        // read by mail templates and third-party code as a display name.
        $this->updateConfigurationValue('PS_SHOP_COUNTRY', 'id_country', ['id_country' => $country !== null ? $country->name : ''] + $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SHOP_STATE_ID', 'id_state', ['id_state' => $state !== null ? $stateId : 0] + $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SHOP_STATE', 'id_state', ['id_state' => $state !== null ? $state->name : ''] + $configuration, $shopConstraint);

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['name', 'email', 'registration_number', 'address1', 'address2', 'postcode', 'city', 'id_country', 'id_state', 'phone', 'fax']);
        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('email', 'string');
        $resolver->setAllowedTypes('registration_number', ['string', 'null']);
        $resolver->setAllowedTypes('address1', ['string', 'null']);
        $resolver->setAllowedTypes('address2', ['string', 'null']);
        $resolver->setAllowedTypes('postcode', ['string', 'null']);
        $resolver->setAllowedTypes('city', ['string', 'null']);
        $resolver->setAllowedTypes('id_country', ['int', 'null']);
        $resolver->setAllowedTypes('id_state', ['int', 'null']);
        $resolver->setAllowedTypes('phone', ['string', 'null']);
        $resolver->setAllowedTypes('fax', ['string', 'null']);

        return $resolver;
    }

    private function isOverriddenForShop(array $configuration, string $field): bool
    {
        return $this->multistoreFeature->isUsed()
            && !$this->shopContext->isAllShopContext()
            && isset($configuration[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field]);
    }

    private function isSavedForContext(array $configuration, string $field): bool
    {
        return !$this->multistoreFeature->isUsed()
            || $this->shopContext->isAllShopContext()
            || isset($configuration[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field]);
    }

    private function isOverrideRemovedForShop(array $configuration, string $field, string $key): bool
    {
        return $this->multistoreFeature->isUsed()
            && !$this->shopContext->isAllShopContext()
            && !isset($configuration[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field])
            && $this->hasShopValue($key);
    }

    private function getInheritedCountryId(): int
    {
        $shopConstraint = $this->shopContext->isShopContext()
            ? ShopConstraint::shopGroup((int) $this->shopContext->getContextShopGroup()->id)
            : ShopConstraint::allShops();

        return (int) $this->configuration->get('PS_SHOP_COUNTRY_ID', null, $shopConstraint);
    }

    /**
     * Whether the key has a value of its own in the current shop or shop group context, not an inherited one.
     */
    private function hasShopValue(string $key): bool
    {
        $shopConstraint = $this->getShopConstraint();
        if ($shopConstraint === null) {
            return false;
        }

        $strictConstraint = $shopConstraint->getShopId() !== null
            ? ShopConstraint::shop($shopConstraint->getShopId()->getValue(), true)
            : ShopConstraint::shopGroup($shopConstraint->getShopGroupId()->getValue(), true);

        return $this->configuration->has($key, $strictConstraint);
    }

    private function collectValidationErrors(array $configuration, ?Country $country, ?State $state, int $countryId): array
    {
        $errors = [];

        if (empty($configuration['name'])) {
            $errors[] = [
                'key' => 'The %s field is required.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"Name"'],
            ];
        } elseif (!Validate::isGenericName($configuration['name'])) {
            $errors[] = [
                'key' => 'The %s field is not valid',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"Name"'],
            ];
        }

        if (empty($configuration['email'])) {
            $errors[] = [
                'key' => 'The %s field is required.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"Email address"'],
            ];
        } elseif (!Validate::isEmail($configuration['email'])) {
            $errors[] = [
                'key' => 'The %s field is not valid',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['"Email address"'],
            ];
        }

        if ($state !== null && (int) $state->id_country !== $countryId) {
            $errors[] = [
                'key' => 'The selected state does not belong to the selected country.',
                'domain' => 'Admin.Shopparameters.Notification',
                'parameters' => [],
            ];
        }

        // Only when this save writes the Country or the State: a shop inheriting both cannot fix them here.
        if ($country !== null && $country->contains_states && $state === null
            && ($this->isSavedForContext($configuration, 'id_country') || $this->isSavedForContext($configuration, 'id_state'))
        ) {
            $errors[] = [
                'key' => 'An address located in a country containing states must have a state selected.',
                'domain' => 'Admin.Shopparameters.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }
}
