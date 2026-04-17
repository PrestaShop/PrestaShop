<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Cookie;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;

/**
 * Manages the configuration data about general options.
 */
class GeneralConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly Cookie $cookie,
        private readonly SymfonyCacheClearer $symfonyCacheClearer,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'check_ip_address' => $this->configuration->getBoolean('PS_COOKIE_CHECKIP'),
            'front_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_FO'),
            'back_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_BO'),
            'cookie_samesite' => $this->configuration->get('PS_COOKIE_SAMESITE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            if (!$this->validateSameSite($configuration['cookie_samesite'])) {
                $errors[] = [
                    'key' => 'The SameSite=None attribute is only available in secure mode.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ];
            } else {
                $this->configuration->set('PS_COOKIE_CHECKIP', (bool) $configuration['check_ip_address']);
                $this->configuration->set('PS_COOKIE_LIFETIME_FO', (int) $configuration['front_cookie_lifetime']);
                $this->configuration->set('PS_COOKIE_LIFETIME_BO', (int) $configuration['back_cookie_lifetime']);
                $this->configuration->set('PS_COOKIE_SAMESITE', $configuration['cookie_samesite']);
                // Clear checksum to force the refresh
                $this->cookie->checksum = '';
                $this->cookie->write();

                // Since the DB value PS_COOKIE_LIFETIME_BO impacts the Symfony security configuration we need to clear the cache
                $this->symfonyCacheClearer->clear();
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $isValid = isset(
            $configuration['check_ip_address'],
            $configuration['front_cookie_lifetime'],
            $configuration['back_cookie_lifetime']
        ) && in_array(
            $configuration['cookie_samesite'],
            CookieOptions::SAMESITE_AVAILABLE_VALUES
        );

        return (bool) $isValid;
    }

    /**
     * Validate SameSite.
     * The SameSite=None is only working when Secure is settled
     *
     * @param string $sameSite
     *
     * @return bool
     */
    protected function validateSameSite(string $sameSite): bool
    {
        if ($sameSite === CookieOptions::SAMESITE_NONE) {
            return $this->configuration->get('PS_SSL_ENABLED');
        }

        return true;
    }
}
