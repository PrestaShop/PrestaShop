<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminFilter.
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="admin_filter_search_id_idx", columns={"employee", "shop", "controller", "action", "filter_id"})})
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AdminFilterRepository")
 */
class AdminFilter
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="employee", type="integer")
     */
    private int $employee;

    /**
     * @ORM\Column(name="shop", type="integer")
     */
    private int $shop;

    /**
     * @ORM\Column(name="controller", type="string", length=60)
     */
    private string $controller;

    /**
     * @ORM\Column(name="action", type="string", length=100)
     */
    private string $action;

    /**
     * @ORM\Column(name="filter", type="text")
     */
    private string $filter;

    /**
     * @ORM\Column(name="filter_id", type="string", length=191)
     */
    private string $filterId = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function setEmployee(int $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getEmployee(): int
    {
        return $this->employee;
    }

    public function setShop(int $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getShop(): int
    {
        return $this->shop;
    }

    public function setController(string $controller): static
    {
        $this->controller = $controller;

        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setFilter(string $filter): static
    {
        $this->filter = $filter;

        return $this;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function getFilterId(): string
    {
        return $this->filterId;
    }

    public function setFilterId(string $filterId): static
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * Gets an array with each filter key needed by Product catalog page.
     *
     * Values are filled with empty strings.
     */
    public static function getProductCatalogEmptyFilter(): array
    {
        return [
            'filter_category' => '',
            'filter_column_id_product' => '',
            'filter_column_name' => '',
            'filter_column_reference' => '',
            'filter_column_name_category' => '',
            'filter_column_price' => '',
            'filter_column_sav_quantity' => '',
            'filter_column_active' => '',
            'last_offset' => 0,
            'last_limit' => 20,
            'last_orderBy' => 'id_product',
            'last_sortOrder' => 'desc',
        ];
    }

    /**
     * Gets an array with filters needed by Product catalog page.
     *
     * The data is decoded and filled with empty strings if there is no value on each entry.
     */
    public function getProductCatalogFilter(): array
    {
        $decoded = json_decode($this->getFilter(), true);

        return array_merge(
            $this->getProductCatalogEmptyFilter(),
            $decoded
        );
    }

    /**
     * Set the filters for Product catalog page into $this->filter.
     *
     * Filters input data to keep only Product catalog filters, and encode it.
     */
    public function setProductCatalogFilter(array $filter): static
    {
        $filter = array_intersect_key(
            $filter,
            $this->getProductCatalogEmptyFilter()
        );
        $filter = self::sanitizeFilterParameters($filter);

        return $this->setFilter(json_encode($filter));
    }

    /**
     * Sanitize filter parameters.
     */
    public static function sanitizeFilterParameters(array $filter): mixed
    {
        $filterMinMax = function ($filter) {
            return function ($subject) use ($filter) {
                $operator = null;

                if (str_contains($subject, '<=')) {
                    $operator = '<=';
                }

                if (str_contains($subject, '>=')) {
                    $operator = '>=';
                }

                if (null === $operator) {
                    $pattern = '#BETWEEN (?P<min>\d+\.?\d*) AND (?P<max>\d+\.?\d*)#';
                    if (0 === preg_match($pattern, $subject, $matches)) {
                        return '';
                    }

                    return sprintf('BETWEEN %f AND %f', $matches['min'], $matches['max']);
                } else {
                    $subjectWithoutOperator = str_replace($operator, '', $subject);

                    $flag = FILTER_DEFAULT;
                    if ($filter === FILTER_SANITIZE_NUMBER_FLOAT) {
                        $flag = FILTER_FLAG_ALLOW_FRACTION;
                    }

                    $filteredSubjectWithoutOperator = filter_var($subjectWithoutOperator, $filter, $flag);
                    if (!$filteredSubjectWithoutOperator) {
                        $filteredSubjectWithoutOperator = 0;
                    }

                    return $operator . $filteredSubjectWithoutOperator;
                }
            };
        };

        $entNoquotesHtmlspecialchars = function (string $subject): string {
            return htmlspecialchars($subject, ENT_NOQUOTES);
        };

        return filter_var_array($filter, [
            'filter_category' => FILTER_SANITIZE_NUMBER_INT,
            'filter_column_id_product' => [
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_INT),
            ],
            'filter_column_name' => [
                'filter' => FILTER_CALLBACK,
                'options' => $entNoquotesHtmlspecialchars,
            ],
            'filter_column_reference' => [
                'filter' => FILTER_CALLBACK,
                'options' => 'htmlspecialchars',
            ],
            'filter_column_name_category' => [
                'filter' => FILTER_CALLBACK,
                'options' => 'htmlspecialchars',
            ],
            'filter_column_price' => [
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_FLOAT),
            ],
            'filter_column_sav_quantity' => [
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_INT),
            ],
            'filter_column_active' => FILTER_SANITIZE_NUMBER_INT,
            'last_offset' => FILTER_SANITIZE_NUMBER_INT,
            'last_limit' => FILTER_SANITIZE_NUMBER_INT,
            'last_orderBy' => [
                'filter' => FILTER_CALLBACK,
                'options' => 'htmlspecialchars',
            ],
            'last_sortOrder' => [
                'filter' => FILTER_CALLBACK,
                'options' => 'htmlspecialchars',
            ],
        ]);
    }
}
