<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\Configuration;

/**
 * Interface PaymentRestrictionsConfigurator defines contract for payment module restrications configurator.
 */
interface PaymentRestrictionsConfiguratorInterface
{
    /**
     * Configure payment module restrictions for currencies.
     *
     * @param array $currencyRestrictions
     *
     * @return bool
     */
    public function configureCurrencyRestrictions(array $currencyRestrictions);

    /**
     * Configure payment module restrictions for countries.
     *
     * @param array $countryRestrictions
     *
     * @return bool
     */
    public function configureCountryRestrictions(array $countryRestrictions);

    /**
     * Configure payment module restrictions for customer groups.
     *
     * @param array $groupRestrictions
     *
     * @return bool
     */
    public function configureGroupRestrictions(array $groupRestrictions);

    /**
     * Configure payment module restrictions for carriers.
     *
     * @param array $groupRestrictions
     *
     * @return bool
     */
    public function configureCarrierRestrictions(array $groupRestrictions);
}
