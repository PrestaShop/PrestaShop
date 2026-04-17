<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            str_starts_with($serviceId, self::IDENTIFIABLE_OBJECT_SERVICE_NAME_START_WITH)
            || str_starts_with($serviceId, self::ALTERNATIVE_IDENTIFIABLE_OBJECT_SERVICE_STARTS_WITH)
        ;

        return $isServiceKeyBelongsToIdentifiableObject
            && is_subclass_of($serviceClass, FormBuilderInterface::class)
        ;
    }
}
