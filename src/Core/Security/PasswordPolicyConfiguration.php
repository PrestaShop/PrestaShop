<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Responsible for saving configuration options for security
 */
class PasswordPolicyConfiguration implements DataConfigurationInterface
{
    public const PASSWORD_EXTREMELY_GUESSABLE = 0;
    public const PASSWORD_VERY_GUESSABLE = 1;
    public const PASSWORD_SOMEWHAT_GUESSABLE = 2;
    public const PASSWORD_SAFELY_UNGUESSABLE = 3;
    public const PASSWORD_VERY_UNGUESSABLE = 4;

    public const DEFAULT_MINIMUM_LENGTH = 8;
    public const DEFAULT_MAXIMUM_LENGTH = 72;

    public const AVAILABLE_PASSWORD_TYPE = [
        self::PASSWORD_EXTREMELY_GUESSABLE,
        self::PASSWORD_VERY_GUESSABLE,
        self::PASSWORD_SOMEWHAT_GUESSABLE,
        self::PASSWORD_SAFELY_UNGUESSABLE,
        self::PASSWORD_VERY_UNGUESSABLE,
    ];

    public const CONFIGURATION_MAXIMUM_LENGTH = 'PS_SECURITY_PASSWORD_POLICY_MAXIMUM_LENGTH';
    public const CONFIGURATION_MINIMUM_LENGTH = 'PS_SECURITY_PASSWORD_POLICY_MINIMUM_LENGTH';
    public const CONFIGURATION_MINIMUM_SCORE = 'PS_SECURITY_PASSWORD_POLICY_MINIMUM_SCORE';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'minimum_length' => $this->configuration->get(static::CONFIGURATION_MINIMUM_LENGTH),
            'maximum_length' => $this->configuration->get(static::CONFIGURATION_MAXIMUM_LENGTH),
            'minimum_score' => $this->configuration->get(static::CONFIGURATION_MINIMUM_SCORE),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(static::CONFIGURATION_MINIMUM_SCORE, $configuration['minimum_score']);
            $minimumLength = min($configuration['minimum_length'], $configuration['maximum_length']);
            $maximumLength = max($configuration['minimum_length'], $configuration['maximum_length']);

            $this->configuration->set(static::CONFIGURATION_MINIMUM_LENGTH, $minimumLength);
            $this->configuration->set(static::CONFIGURATION_MAXIMUM_LENGTH, $maximumLength);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset($configuration['minimum_score'])
            && in_array($configuration['minimum_score'], static::AVAILABLE_PASSWORD_TYPE)
            && isset($configuration['maximum_length'])
            && $configuration['maximum_length'] >= 1 && $configuration['maximum_length'] <= 100
            && isset($configuration['minimum_length'])
            && $configuration['minimum_length'] >= 1 && $configuration['minimum_length'] <= 100
        ;
    }
}
