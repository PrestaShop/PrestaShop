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

namespace PrestaShop\PrestaShop\Adapter\Media;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will provide Media servers configuration for a Shop
 */
class MediaServerConfiguration implements DataConfigurationInterface
{
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
        return array(
            'media_server_one' => $this->configuration->get('PS_MEDIA_SERVER_1'),
            'media_server_two' => $this->configuration->get('PS_MEDIA_SERVER_2'),
            'media_server_three' => $this->configuration->get('PS_MEDIA_SERVER_3'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();
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
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $errors = array();
        $serverOne = $configuration['media_server_one'];
        $serverTwo = $configuration['media_server_two'];
        $serverThree = $configuration['media_server_three'];

        if (!empty($serverOne) && !filter_var($serverOne, FILTER_VALIDATE_URL)) {
            $errors[] = array(
                'key' => 'Media server #1 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => array(),
            );
        }

        if (!empty($serverTwo) && !filter_var($serverTwo, FILTER_VALIDATE_URL)) {
            $errors[] = array(
                'key' => 'Media server #2 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => array(),
            );
        }

        if (!empty($serverThree) && !filter_var($serverThree, FILTER_VALIDATE_URL)) {
            $errors[] = array(
                'key' => 'Media server #3 is invalid',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => array(),
            );
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return true;
    }
}
