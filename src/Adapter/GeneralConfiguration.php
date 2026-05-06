<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Cookie;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about general options.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'check_ip_address',
        'front_cookie_lifetime',
        'back_cookie_lifetime',
        'cookie_samesite',
    ];

    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        private readonly Cookie $cookie,
        private readonly SymfonyCacheClearer $symfonyCacheClearer,
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'check_ip_address' => (bool) $this->configuration->get('PS_COOKIE_CHECKIP', false, $shopConstraint),
            'front_cookie_lifetime' => (int) $this->configuration->get('PS_COOKIE_LIFETIME_FO', 0, $shopConstraint),
            'back_cookie_lifetime' => (int) $this->configuration->get('PS_COOKIE_LIFETIME_BO', 0, $shopConstraint),
            'cookie_samesite' => (string) $this->configuration->get('PS_COOKIE_SAMESITE', CookieOptions::SAMESITE_LAX, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            if (!$this->validateSameSite($configuration['cookie_samesite'])) {
                $errors[] = [
                    'key' => 'The SameSite=None attribute is only available in secure mode.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ];
            } else {
                $this->updateConfigurationValue('PS_COOKIE_CHECKIP', 'check_ip_address', $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_LIFETIME_FO', 'front_cookie_lifetime', $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_LIFETIME_BO', 'back_cookie_lifetime', $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_SAMESITE', 'cookie_samesite', $configuration, $shopConstraint);
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
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('check_ip_address', 'bool')
            ->setAllowedTypes('front_cookie_lifetime', 'int')
            ->setAllowedTypes('back_cookie_lifetime', 'int')
            ->setAllowedTypes('cookie_samesite', 'string')
            ->setAllowedValues('cookie_samesite', CookieOptions::SAMESITE_AVAILABLE_VALUES);
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
