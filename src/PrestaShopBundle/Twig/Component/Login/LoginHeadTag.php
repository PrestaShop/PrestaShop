<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Component\Login;

use PrestaShopBundle\Twig\Component\HeadTag;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LoginLayout/head_tag.html.twig')]
class LoginHeadTag extends HeadTag
{
    protected const SET_MEDIA_HOOK = 'actionAdminLoginControllerSetMedia';

    public function mount(string $metaTitle = ''): void
    {
        $this->metaTitle = $metaTitle;
        $this->hookDispatcher->dispatchWithParameters(
            static::SET_MEDIA_HOOK,
            [
                'controller' => $this->legacyControllerContext,
            ]
        );
    }
}
