<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @param bool|null $isDebug
     * @param string|null $name
     */
    public function __construct($isDebug = null, $name = null)
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

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        if (defined('_PS_CACHE_DIR_')) {
            return _PS_CACHE_DIR_;
        }

        return _PS_ROOT_DIR_ . '/var/cache/' . $this->getName() . '/';
    }
}
