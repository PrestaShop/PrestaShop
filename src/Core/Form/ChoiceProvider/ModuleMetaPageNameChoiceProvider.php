<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
