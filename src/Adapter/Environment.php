<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    private $environment;

    /**
     * @param bool|null $isDebug
     * @param string|null $environment
     */
    public function __construct($isDebug = null, $environment = null)
    {
        if (null === $isDebug) {
            $this->isDebug = defined(_PS_MODE_DEV_) ? (bool) _PS_MODE_DEV_ : true;
        } else {
            $this->isDebug = (bool) $isDebug;
        }

        $this->environment = $environment;
        if (null === $this->environment) {
            if (!empty($_SERVER['APP_ENV'])) {
                $this->environment = $_SERVER['APP_ENV'];
            } elseif (defined('_PS_IN_TEST_') && _PS_IN_TEST_) {
                $this->environment = 'test';
            } else {
                $this->environment = $this->isDebug ? 'dev' : 'prod';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->isDebug;
    }
}
