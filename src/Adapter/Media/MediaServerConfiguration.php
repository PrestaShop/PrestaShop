<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Media;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will provide Media servers configuration for a Shop.
 */
class MediaServerConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'media_server_one' => $this->configuration->get('PS_MEDIA_SERVER_1'),
            'media_server_two' => $this->configuration->get('PS_MEDIA_SERVER_2'),
            'media_server_three' => $this->configuration->get('PS_MEDIA_SERVER_3'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        $isValid = $this->validateConfiguration($configuration);
        if (true === $isValid) {
            $serverOne = $configuration['media_server_one'];
            $serverTwo = $configuration['media_server_two'];
            $serverThree = $configuration['media_server_three'];

            $this->configuration->set('PS_MEDIA_SERVER_1', $serverOne);
            $this->configuration->set('PS_MEDIA_SERVER_2', $serverTwo);
            $this->configuration->set('PS_MEDIA_SERVER_3', $serverThree);

            if (!empty($serverOne) || !empty($serverTwo) || !empty($serverThree)) {
                $this->configuration->set('PS_MEDIA_SERVERS', 1);
            } else {
                $this->configuration->set('PS_MEDIA_SERVERS', 0);
            }
        } else {
            $errors = $isValid;
        }

        return $errors;
    }

    /**
     * @param array $configuration
     *
     * @return array<int, array<string, array|string>>|bool
     */
    public function validateConfiguration(array $configuration)
    {
        $errors = [];
        $serverOne = $configuration['media_server_one'];
        $serverTwo = $configuration['media_server_two'];
        $serverThree = $configuration['media_server_three'];

        if (!empty($serverOne) && !$this->isValidDomain($serverOne)) {
            $errors[] = [
                'key' => 'Media server #1 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        if (!empty($serverTwo) && !$this->isValidDomain($serverTwo)) {
            $errors[] = [
                'key' => 'Media server #2 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        if (!empty($serverThree) && !$this->isValidDomain($serverThree)) {
            $errors[] = [
                'key' => 'Media server #3 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return true;
    }

    /**
     * @param string $domainName
     *
     * @return bool
     */
    private function isValidDomain($domainName)
    {
        if (false !== filter_var($domainName, FILTER_VALIDATE_DOMAIN)) {
            return false !== filter_var(gethostbyname($domainName), FILTER_VALIDATE_IP);
        }

        return false;
    }
}
