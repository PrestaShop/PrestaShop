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

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Providers service ids of only identifiable object form builder.
 */
final class IdentifiableObjectFormBuilderServiceDefinitionProvider implements ServiceDefinitionProviderInterface
{
    const FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER = 0;

    const SERVICE_NAME_START_WITH = 'prestashop.core.form.identifiable_object.builder';
    const ALTERNATIVE_SERVICE_STARTS_WITH = 'prestashop.core.form.builder';

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

            if (!$this->isIdentifiableObjectFormBuilderServiceKey($serviceId)) {
                continue;
            }

            if (!is_subclass_of($serviceDefinition->getClass(), FormBuilderInterface::class)) {
                continue;
            }

            $filteredDefinitions[$serviceId] = $serviceDefinition;
        }

        return $filteredDefinitions;
    }

    /**
     * Checks if service belongs to identifiable object form builder.
     *
     * @param string $serviceId
     *
     * @return bool
     */
    private function isIdentifiableObjectFormBuilderServiceKey($serviceId)
    {
        return strpos($serviceId, self::SERVICE_NAME_START_WITH) === 0 ||
            strpos($serviceId, self::ALTERNATIVE_SERVICE_STARTS_WITH) === 0
        ;
    }
}
