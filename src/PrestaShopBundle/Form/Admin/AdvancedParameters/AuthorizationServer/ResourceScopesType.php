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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\AuthorizationServer;

use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShopBundle\ApiPlatform\Scopes\ResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ResourceScopesExtractor;
use PrestaShopBundle\Form\Admin\Type\AccordionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResourceScopesType extends TranslatorAwareType implements DataMapperInterface
{
    private const CORE_FILE_NAME = '__core_scopes';

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ResourceScopesExtractor $resourceScopeExtractor,
        private readonly ModuleRepository $moduleRepository
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resourceScopes = $this->resourceScopeExtractor->getAllResourceScopes();
        foreach ($resourceScopes as $resourceScope) {
            $builder->add($this->getResourceFormName($resourceScope), CollectionType::class, [
                'label' => $this->getResourceLabel($resourceScope),
                'label_subtitle' => $this->getResourceLabelSubtitle($resourceScope),
                'entry_type' => SwitchScopeType::class,
            ]);
        }
        $builder->setDataMapper($this);
    }

    public function mapDataToForms($viewData, \Traversable $forms)
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $resources = $this->resourceScopeExtractor->getAllResourceScopes();
        foreach ($resources as $resource) {
            $resourceForm = $forms[$this->getResourceFormName($resource)];
            $formattedData = [];
            foreach ($resource->getScopes() as $scope) {
                $formattedData[] = [
                    'scope' => $scope,
                    'associated' => is_array($viewData) && in_array($scope, $viewData),
                ];
            }
            $resourceForm->setData($formattedData);
        }
    }

    public function mapFormsToData(\Traversable $forms, &$viewData)
    {
        $associatedScopes = [];
        /** @var FormInterface $collection */
        foreach ($forms as $collection) {
            foreach ($collection->all() as $scopeForm) {
                if ($scopeForm->get('associated')->getData() === true) {
                    $associatedScopes[] = $scopeForm->get('scope')->getData();
                }
            }
        }
        $viewData = $associatedScopes;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'expand_all' => true,
            'display_one' => false,
        ]);
    }

    public function getParent()
    {
        return AccordionType::class;
    }

    private function getResourceFormName(ResourceScopes $resourceScopes): string
    {
        return $resourceScopes->fromCore() ? self::CORE_FILE_NAME : $resourceScopes->getModuleName();
    }

    private function getResourceLabel(ResourceScopes $resourceScopes): string
    {
        if ($resourceScopes->fromCore()) {
            return $this->trans('Native scopes', 'Admin.Advparameters.Feature');
        }

        $module = $this->moduleRepository->getModule($resourceScopes->getModuleName());

        return $module->attributes->get('displayName');
    }

    private function getResourceLabelSubtitle(ResourceScopes $resourceScopes): string
    {
        if ($resourceScopes->fromCore()) {
            return '';
        }

        $module = $this->moduleRepository->getModule($resourceScopes->getModuleName());

        return $module->database->get('active') === true ?
            $this->trans('Enabled', 'Admin.Global') :
            $this->trans('Disabled', 'Admin.Global');
    }
}
