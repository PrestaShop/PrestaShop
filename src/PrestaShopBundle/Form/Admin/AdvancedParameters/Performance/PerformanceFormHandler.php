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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShop\PrestaShop\Adapter\Feature\CombinationFeature;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This class manages the data manipulated using forms
 * in "Configure > Advanced Parameters > Performance" page.
 *
 * @deprecated since 1.7.4.0, to be removed in the next major
 */
final class PerformanceFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CombinationFeature
     */
    private $combinationFeature;

    /**
     * @var FormDataProviderInterface
     */
    private $formDataProvider;

    public function __construct(
        FormFactoryInterface $formFactory,
        FormDataProviderInterface $formDataProvider,
        CombinationFeature $combinationFeature
    ) {
        $this->formFactory = $formFactory;
        $this->combinationFeature = $combinationFeature;
        $this->formDataProvider = $formDataProvider;
    }

    public function getForm()
    {
        $formBuilder = $this->formFactory->createBuilder()
            ->add('smarty', SmartyType::class)
            ->add('debug_mode', DebugModeType::class)
            ->add('optional_features', OptionalFeaturesType::class, [
                'are_combinations_used' => $this->combinationFeature->isUsed(),
            ])
            ->add('ccc', CombineCompressCacheType::class)
            ->add('media_servers', MediaServersType::class)
            ->add('caching', CachingType::class)
            ->add('add_memcache_server', MemcacheServerType::class)
            ->setData($this->formDataProvider->getData());

        return $formBuilder->setData($formBuilder->getData())->getForm();
    }

    public function save(array $data)
    {
        return $this->formDataProvider->setData($data);
    }
}
