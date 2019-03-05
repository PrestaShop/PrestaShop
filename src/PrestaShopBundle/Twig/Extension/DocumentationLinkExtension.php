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

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction;

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
            new Twig_SimpleFunction(
                'documentation_link',
                [$this->documentationLinkProvider, 'getLink']
            ),
        ];
    }
}
