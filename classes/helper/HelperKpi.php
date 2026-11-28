<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HelperKpiCore extends Helper
{
    /**
     * @var string
     */
    public $base_folder = 'helpers/kpi/';
    /**
     * @var string
     */
    public $base_tpl = 'kpi.tpl';

    public $id;
    public $icon;
    public $chart;
    public $color;
    public $title;
    public $subtitle;
    public $value;
    public $data;
    public $source;
    public $refresh = true;
    public $href;
    public $tooltip;

    public function generate()
    {
        if (!$this->canReadSource()) {
            return '';
        }

        $this->tpl = $this->createTemplate($this->base_tpl);

        $this->tpl->assign([
            'id' => $this->id,
            'icon' => $this->icon,
            'chart' => (bool) $this->chart,
            'color' => $this->color,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'value' => $this->value,
            'data' => $this->data,
            'source' => $this->source,
            'refresh' => $this->refresh,
            'href' => $this->href,
            'tooltip' => $this->tooltip,
        ]);

        return $this->tpl->fetch();
    }

    protected function canReadSource(): bool
    {
        parse_str((string) parse_url((string) $this->source, PHP_URL_QUERY), $params);
        $controller = $params['controller'] ?? '';

        return !is_string($controller)
            || !Tab::getIdFromClassName($controller)
            || $this->context->employee->can('view', $controller);
    }
}
