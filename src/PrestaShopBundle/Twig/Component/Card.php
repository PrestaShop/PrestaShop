<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Generic card: a title/icon header with an optional "Configure" link, and a free-form body
 * passed as the `content` block. Not specific to the dashboard — reusable anywhere in the admin.
 *
 * {% component 'Card' with {title: 'Sales', icon: 'insights', configUrl: url} %}
 *   {% block content %}...{% endblock %}
 * {% endcomponent %}
 */
#[AsTwigComponent(template: '@PrestaShop/Admin/Component/card.html.twig')]
class Card extends AbstractCard
{
    public function mount(string $title = '', ?string $icon = null, ?string $configUrl = null, ?string $configLabel = null): void
    {
        $this->mountCard($title, $icon, $configUrl, $configLabel);
    }
}
