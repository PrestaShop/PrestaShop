<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Legacy;

use PrestaShopBundle\Twig\Component\SearchForm;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/search_form.html.twig')]
class LegacySearchForm extends SearchForm
{
}
