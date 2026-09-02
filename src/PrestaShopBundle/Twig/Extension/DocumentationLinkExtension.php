<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds "documentation_link" function to Twig so documentation links can be generated in templates
 */
class DocumentationLinkExtension extends AbstractExtension
{
    /**
     * @var DocumentationLinkProviderInterface
     */
    private $documentationLinkProvider;

    /**
     * @param DocumentationLinkProviderInterface $documentationLinkProvider
     */
    public function __construct(DocumentationLinkProviderInterface $documentationLinkProvider)
    {
        $this->documentationLinkProvider = $documentationLinkProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'documentation_link',
                [$this->documentationLinkProvider, 'getLink']
            ),
        ];
    }
}
