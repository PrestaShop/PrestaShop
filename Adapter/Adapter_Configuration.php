<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;

class Adapter_Configuration implements Core_Business_ConfigurationInterface
{
    /**
     * Returns constant defined by given $key if exists or check directly into PrestaShop
     * Configuration
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (defined($key)) {
            return constant($key);
        } else {
            return Configuration::get($key);
        }
    }

    /**
     * Set a data in the persistence layer (Cookie for now...)
     *
     * @param string $key
     * @param mixed $value
     * @throws Core_Foundation_Exception_Exception If the size exceeds 3kB weight after json_encode.
     * @return Adapter_Configuration $this, for fluent chaining setter.
     */
    public function persistUserData($key, $value)
    {
        $legacyCookie = \Context::getContext()->cookie;
        if ($value !== null && strlen($value) > 3072) {
            $legacyCookie->__unset('user_persistence_'.$key);
            throw new Core_Foundation_Exception_Exception('The value to persist take more than 3kB to store in the Cookie.');
        }

        if ($value === null) {
            $legacyCookie->__unset('user_persistence_'.$key);
        } else {
            $legacyCookie->__set('user_persistence_'.$key, $value);
        }
        return $this;
    }

    /**
     * Get user data persisted in the Cookie. Null if no data known for the key.
     *
     * @param string $key
     * @return mixed|null Null if the key has no value.
     */
    public function getPersistedUserData($key)
    {
        $legacyCookie = \Context::getContext()->cookie;
        return $legacyCookie->__isset('user_persistence_'.$key) ? $legacyCookie->__get('user_persistence_'.$key) : null;
    }
}
