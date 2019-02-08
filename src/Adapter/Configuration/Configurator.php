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

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use Configuration;
use PrestaShop\PrestaShop\Core\Configuration\ConfiguratorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Implementation of shop configurator using legacy object model
 *
 * @internal
 */
final class Configurator implements ConfiguratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null, array $options = [])
    {
        if (defined($key)) {
            return constant($key);
        }

        $options = $this->resolveGetOptions($options);

        // if the key is multi lang related, we return an array with the value per language.
        // getInt() meaning probably getInternational()
        if (Configuration::isLangKey($key)) {
            return Configuration::getInt(
                $key,
                $options['shop_group_id'],
                $options['shop_id']
            );
        }

        if (Configuration::hasKey($key)) {
            return Configuration::get(
                $key,
                $options['language_id'],
                $options['shop_group_id'],
                $options['shop_id'],
                $default
            );
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, array $options = [])
    {
        $options = $this->resolveSetOptions($options);

        return Configuration::updateValue(
            $key,
            $value,
            $options['is_html'],
            $options['shop_group_id'],
            $options['shop_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key, array $options = [])
    {
        return Configuration::deleteByName($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getInt($key, $default = 0, array $options = [])
    {
        return (int) $this->get($key, $default, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBool($key, $default = false, array $options = [])
    {
        return (bool) $this->get($key, $default, $options);
    }

    /**
     * Resolves options for setting configuration
     *
     * @param array $options
     *
     * @return array Resolved options
     */
    private function resolveSetOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'is_html' => false,
                'shop_id' => null,
                'shop_group_id' => null,
            ])
            ->setAllowedValues('is_html', [true, false])
            ->setAllowedTypes('shop_id', ['null', 'int'])
            ->setAllowedTypes('shop_group_id', ['null', 'int'])
        ;

        return $resolver->resolve($options);
    }

    /**
     * Resolves options for setting configuration
     *
     * @param array $options
     *
     * @return array Resolved options
     */
    private function resolveGetOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'language_id' => false,
                'shop_id' => null,
                'shop_group_id' => null,
            ])
            ->setAllowedTypes('laanguage_id', ['null', 'int'])
            ->setAllowedTypes('shop_id', ['null', 'int'])
            ->setAllowedTypes('shop_group_id', ['null', 'int'])
        ;

        return $resolver->resolve($options);
    }
}
