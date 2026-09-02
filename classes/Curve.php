<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Data structure to store curves
 */
class CurveCore
{
    /**
     * @var float[] indexed by string
     */
    protected $values = [];
    /**
     * @var string
     */
    protected $label;
    /**
     * Can be: bars, steps
     *
     * @var string
     */
    protected $type;

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @param bool $time_mode
     *
     * @return string
     */
    public function getValues($time_mode = false)
    {
        ksort($this->values);
        $string = '';
        foreach ($this->values as $key => $value) {
            $string .= '[' . addslashes((string) $key) . ($time_mode ? '000' : '') . ',' . (float) $value . '],';
        }

        return '{data:[' . rtrim($string, ',') . ']'
            . (!empty($this->label) ? ',label:"' . $this->label . '"' : '') . ''
            . (!empty($this->type) ? ',' . $this->type : '') . '}';
    }

    /**
     * @param string $x
     * @param float $y
     */
    public function setPoint($x, $y)
    {
        $this->values[(string) $x] = (float) $y;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @param string $type accepts only 'bars' or 'steps'
     */
    public function setType($type)
    {
        $this->type = '';
        if ($type == 'bars') {
            $this->type = 'bars:{show:true,lineWidth:10}';
        }
        if ($type == 'steps') {
            $this->type = 'lines:{show:true,steps:true}';
        }
    }

    /**
     * @param string $x
     *
     * @return float|null return point if found, null else
     */
    public function getPoint($x)
    {
        if (array_key_exists((string) $x, $this->values)) {
            return $this->values[(string) $x];
        }

        return null;
    }
}
