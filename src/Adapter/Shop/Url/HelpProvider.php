<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generates the help url which is used most in the help sidebar component.
 */
class HelpProvider implements UrlProviderInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Documentation
     */
    private $documentation;

    public function __construct(
        LegacyContext $legacyContext,
        TranslatorInterface $translator,
        RouterInterface $router,
        Documentation $documentation
    ) {
        $this->legacyContext = $legacyContext;
        $this->translator = $translator;
        $this->router = $router;
        $this->documentation = $documentation;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(string $section = '', string $title = '')
    {
        if (empty($title)) {
            $title = $this->translator->trans('Help', [], 'Admin.Global');
        }

        $url = $this->router->generate('admin_common_sidebar', [
            'url' => $this->documentation->generateLink($section, $this->legacyContext->getEmployeeLanguageIso()),
            'title' => $title,
        ]);

        // this line is allow to revert a new behaviour introduce in sf 5.4 which break the result we used to have
        return strtr($url, ['%2F' => '%252F']);
    }
}
