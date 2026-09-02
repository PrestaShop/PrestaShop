<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\EnvironmentInterface;

/**
 * Class Environment is used to store/access environment information like the current
 * environment name or to know if debug mode is enabled. It can be built via
 * dependency injection but it also manages default fallback based on legacy PrestaShop
 * const.
 */
class Environment implements EnvironmentInterface
{
    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $appId;

    public function __construct(?bool $isDebug = null, ?string $name = null, ?string $appId = null)
    {
        if (null === $isDebug) {
            $this->isDebug = defined('_PS_MODE_DEV_') ? _PS_MODE_DEV_ : true;
        } else {
            $this->isDebug = $isDebug;
        }

        if (null !== $name) {
            $this->name = $name;
        } else {
            if (defined('_PS_ENV_')) {
                $this->name = _PS_ENV_;
            } else {
                $this->name = $this->isDebug ? 'dev' : 'prod';
            }
        }

        if (null !== $appId) {
            $this->appId = $appId;
        } else {
            if (defined('_PS_APP_ID_')) {
                $this->appId = _PS_APP_ID_;
            } else {
                $this->appId = 'admin';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->isDebug;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        if (defined('_PS_CACHE_DIR_')) {
            return _PS_CACHE_DIR_;
        }

        return _PS_ROOT_DIR_ . '/var/cache/' . $this->getName() . '/' . $this->getAppId() . '/';
    }
}
