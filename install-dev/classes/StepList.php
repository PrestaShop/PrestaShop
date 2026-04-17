<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class StepList implements IteratorAggregate
{
    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    private $stepNames = [];

    /**
     * @param array $stepConfig
     */
    public function __construct(array $stepConfig)
    {
        foreach ($stepConfig as $key => $config) {
            $this->stepNames[$key] = $config['name'];
            $this->steps[$key] = new Step();
            $this->steps[$key]->setName($config['name']);
            $this->steps[$key]->setDisplayName($config['displayName']);
            $this->steps[$key]->setControllerName($config['controllerClass']);
        }
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return StepList
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $stepName
     *
     * @return StepList
     */
    public function setOffsetFromStepName($stepName)
    {
        $this->offset = (int) array_search($stepName, $this->stepNames);

        return $this;
    }

    /**
     * @param string $stepName
     *
     * @return int
     */
    public function getOffsetFromStepName($stepName)
    {
        return (int) array_search($stepName, $this->stepNames);
    }

    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @return Step
     */
    public function current()
    {
        return $this->steps[$this->offset];
    }

    /**
     * @return self
     */
    public function next()
    {
        if (array_key_exists($this->offset + 1, $this->steps)) {
            ++$this->offset;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function previous()
    {
        if (array_key_exists($this->offset - 1, $this->steps)) {
            --$this->offset;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isFirstStep()
    {
        return 0 == $this->offset;
    }

    /**
     * @return bool
     */
    public function isLastStep()
    {
        return $this->offset == count($this->steps) - 1;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->steps);
    }
}
