<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Hook;
use Module;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides choices of available module hooks
 */
final class ModuleHookByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);

        try {
            $hooks_list = Hook::getHooks();
            $hookableList = [];

            $moduleInstance = Module::getInstanceById($options['id_module']);
            foreach ($hooks_list as $hook) {
                $hookName = trim($hook['name']);
                if ('' !== $hook['description']) {
                    $hookName .= ' (' . $hook['description'] . ')';
                }
                if ($moduleInstance->isHookableOn($hookName)) {
                    $hookableList[$hookName] = $hook['id_hook'];
                }
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'An error occurred when getting hooks for module id "%s"',
                    $resolvedOptions['id_module'])
            );
        }

        return $hookableList;
    }

    /**
     * Configures array parameters and default values
     *
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('id_module');
        $resolver->setAllowedTypes('id_module', 'int');
        $this->allowIdCountryGreaterThanZero($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function allowIdCountryGreaterThanZero(OptionsResolver $resolver)
    {
        $resolver->setAllowedValues('id_module', function ($value) {
            return 0 < $value;
        });
    }
}
