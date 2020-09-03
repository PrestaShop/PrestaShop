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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Meta\MetaDataProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ModuleMetaPageNameChoiceProvider is responsible for providing module page choices in
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> form.
 */
final class ModuleMetaPageNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var MetaDataProviderInterface
     */
    private $dataProvider;

    /**
     * DefaultPageChoiceProvider constructor.
     *
     * @param RequestStack $requestStack
     * @param MetaDataProviderInterface $dataProvider
     */
    public function __construct(
        RequestStack $requestStack,
        MetaDataProviderInterface $dataProvider
    ) {
        $this->requestStack = $requestStack;
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $defaultPages = $this->dataProvider->getNotConfiguredModuleMetaPageNames();
        $currentPage = $this->getCurrentPage();

        if (null !== $currentPage) {
            $defaultPages[str_replace('module-', '', $currentPage)] = $currentPage;
            asort($defaultPages);
        }

        return $defaultPages;
    }

    /**
     * Gets current page.
     *
     * @return string|null
     */
    private function getCurrentPage()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        $metaId = null;
        if (null !== $currentRequest) {
            $metaId = $currentRequest->attributes->get('metaId');
        }

        if ($metaId) {
            return $this->dataProvider->getModuleMetaPageNameById($metaId);
        }

        return null;
    }
}
