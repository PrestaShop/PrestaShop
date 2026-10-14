<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Login;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LoginLayout/head_tag.html.twig')]
final class TwoFactorHeadTag extends LoginHeadTag
{
    protected const SET_MEDIA_HOOK = 'actionAdminTwoFactorControllerSetMedia';
}
