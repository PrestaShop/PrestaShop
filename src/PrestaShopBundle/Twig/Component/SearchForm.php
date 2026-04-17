<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/search_form.html.twig')]
class SearchForm
{
    protected const BO_QUERY_PARAM = 'bo_query';
    protected const BO_SEARCH_TYPE_PARAM = 'bo_search_type';

    public string $boQuery;
    public bool $showClearBtn;
    public int $searchType;

    public function __construct(protected readonly RequestStack $requestStack)
    {
    }

    public function mount(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->boQuery = $request->query->get(self::BO_QUERY_PARAM, '');
        $this->searchType = (int) $request->query->get(self::BO_SEARCH_TYPE_PARAM, 0);
        $this->showClearBtn = !empty($this->boQuery);
    }
}
