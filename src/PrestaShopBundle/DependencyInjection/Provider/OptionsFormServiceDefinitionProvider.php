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

namespace PrestaShopBundle\DependencyInjection\Provider;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Returns service definitions form option forms.
 */
final class OptionsFormServiceDefinitionProvider implements ServiceDefinitionProviderInterface
{
    const OPTIONS_FORM_SERVICE_ENDS_WITH = 'form_handler';

    /**
     * {@inheritdoc}
     */
    public function getDefinitions(ContainerBuilder $containerBuilder)
    {
        $definitions = $containerBuilder->getDefinitions();

        $filteredDefinitions = [];
        foreach ($definitions as $serviceId => $serviceDefinition) {
            if ($serviceDefinition->isAbstract()  || $serviceDefinition->isPrivate()) {
                continue;
            }

            if (!$this->stringEndsWith($serviceId, self::OPTIONS_FORM_SERVICE_ENDS_WITH)) {
                continue;
            }

            if (!is_subclass_of($serviceDefinition->getClass(), FormHandlerInterface::class)) {
                continue;
            }

            $filteredDefinitions[$serviceId] = $serviceDefinition;
        }

        return $filteredDefinitions;
    }

    private function stringEndsWith($haystack, $needle)
    {
        $diff = \strlen($haystack) - \strlen($needle);

        return $diff >= 0 && strpos($haystack, $needle, $diff) !== false;
    }
}
