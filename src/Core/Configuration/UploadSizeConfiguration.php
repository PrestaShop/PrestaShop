<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

class UploadSizeConfiguration implements UploadSizeConfigurationInterface
{
    /**
     * @var IniConfiguration
     */
    private $iniConfiguration;

    /**
     * @param IniConfiguration $iniConfiguration
     */
    public function __construct(IniConfiguration $iniConfiguration)
    {
        $this->iniConfiguration = $iniConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxUploadSizeInBytes(): int
    {
        return $this->iniConfiguration->getUploadMaxSizeInBytes();
    }

    /**
     * {@inheritdoc}
     */
    public function getPostMaxSizeInBytes(): int
    {
        return $this->iniConfiguration->getPostMaxSizeInBytes();
    }
}
