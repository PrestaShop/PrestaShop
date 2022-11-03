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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Collects services information which contains hooks.
 */
class IdentifiableObjectFormTypesCollectorPass implements CompilerPassInterface
{
    public const IDENTIFIABLE_OBJECT_SERVICE_NAME_START_WITH = 'prestashop.core.form.identifiable_object.builder';
    public const ALTERNATIVE_IDENTIFIABLE_OBJECT_SERVICE_STARTS_WITH = 'prestashop.core.form.builder';
    public const GRID_DEFINITION_SERVICE_STARTS_WITH = 'prestashop.core.grid.definition';
    public const FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER = 0;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }

        $serviceDefinitions = $container->getDefinitions();

        $formTypes = [];
        foreach ($serviceDefinitions as $serviceId => $serviceDefinition) {
            if ($serviceDefinition->isAbstract() || $serviceDefinition->isPrivate()) {
                continue;
            }

            if (!$this->isIdentifiableObjectFormBuilderService($serviceId, $serviceDefinition->getClass())) {
                continue;
            }

            $formType = $serviceDefinition->getArgument(self::FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER);

            if (!is_string($formType) || !is_subclass_of($formType, FormTypeInterface::class)) {
                continue;
            }

            $formTypes[] = $formType;
        }

        $container->setParameter(
            'prestashop.core.form.identifiable_object.form_types',
            $formTypes
        );
    }

    /**
     * Checks if service belongs to identifiable object form builder.
     *
     * @param string $serviceId
     * @param string $serviceClass
     *
     * @return bool
     */
    private function isIdentifiableObjectFormBuilderService($serviceId, $serviceClass)
    {
        $isServiceKeyBelongsToIdentifiableObject =
            strpos($serviceId, self::IDENTIFIABLE_OBJECT_SERVICE_NAME_START_WITH) === 0 ||
            strpos($serviceId, self::ALTERNATIVE_IDENTIFIABLE_OBJECT_SERVICE_STARTS_WITH) === 0
        ;

        return $isServiceKeyBelongsToIdentifiableObject &&
            is_subclass_of($serviceClass, FormBuilderInterface::class)
        ;
    }
}
