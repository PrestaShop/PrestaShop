<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Adapter\MailTemplate\EmailBodyTemplateRepository;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class EmailBodyTemplateGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly EmailBodyTemplateRepository $repository,
        private readonly string $defaultLocale,
    ) {
    }

    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $locale = $this->resolveLocale($searchCriteria);
        $templates = $this->repository->findAllForLocale($locale);

        $templates = $this->applyFilters($templates, $searchCriteria);
        $totalCount = count($templates);
        $templates = $this->applySorting($templates, $searchCriteria);
        $templates = $this->applyPagination($templates, $searchCriteria);

        $records = array_map(
            static fn (array $template) => array_merge($template, [
                'has_html_label' => $template['has_html'] ? '✓' : '✗',
                'has_txt_label' => $template['has_txt'] ? '✓' : '✗',
                'source_with_module' => $template['source'] === 'module'
                    ? $template['source'] . ':' . $template['module_name']
                    : $template['source'],
                'locale' => $locale,
            ]),
            $templates,
        );

        return new GridData(
            new RecordCollection($records),
            $totalCount,
        );
    }

    private function resolveLocale(SearchCriteriaInterface $searchCriteria): string
    {
        $filters = $searchCriteria->getFilters();

        if (isset($filters['locale']) && !empty($filters['locale'])) {
            return $filters['locale'];
        }

        return $this->defaultLocale;
    }

    private function applyFilters(array $templates, SearchCriteriaInterface $searchCriteria): array
    {
        $filters = $searchCriteria->getFilters();

        if (isset($filters['template_name']) && !empty($filters['template_name'])) {
            $search = strtolower($filters['template_name']);
            $templates = array_filter(
                $templates,
                static fn (array $t) => str_contains(strtolower($t['template_name']), $search),
            );
        }

        if (isset($filters['source']) && !empty($filters['source'])) {
            $source = $filters['source'];
            $templates = array_filter(
                $templates,
                static fn (array $t) => $t['source'] === $source,
            );
        }

        if (isset($filters['module_name']) && !empty($filters['module_name'])) {
            $search = strtolower($filters['module_name']);
            $templates = array_filter(
                $templates,
                static fn (array $t) => str_contains(strtolower($t['module_name']), $search),
            );
        }

        return array_values($templates);
    }

    private function applySorting(array $templates, SearchCriteriaInterface $searchCriteria): array
    {
        $orderBy = $searchCriteria->getOrderBy();
        $orderWay = $searchCriteria->getOrderWay();

        if (null === $orderBy) {
            return $templates;
        }

        usort($templates, static function (array $a, array $b) use ($orderBy, $orderWay) {
            $result = strcmp((string) ($a[$orderBy] ?? ''), (string) ($b[$orderBy] ?? ''));

            return strtolower($orderWay ?? 'asc') === 'desc' ? -$result : $result;
        });

        return $templates;
    }

    private function applyPagination(array $templates, SearchCriteriaInterface $searchCriteria): array
    {
        $offset = $searchCriteria->getOffset() ?? 0;
        $limit = $searchCriteria->getLimit();

        if (null !== $limit) {
            return array_slice($templates, $offset, $limit);
        }

        return $templates;
    }
}
