<?php

class StepList implements IteratorAggregate
{
    /**
     *
     * @var integer
     */
    protected $offset = 0;

    /**
     *
     * @var array
     */
    protected $steps = array();

    /**
     *
     * @var array
     */
    private $stepNames = array();

    /**
     *
     * @param array $stepNames
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
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param int $offset
     * @return StepList
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     *
     * @param string $stepName
     * @return StepList
     */
    public function setOffsetFromStepName($stepName)
    {
        $this->offset = (int) array_search($stepName, $this->stepNames);

        return $this;
    }

    /**
     *
     * @param string $stepName
     * @return int
     */
    public function getOffsetFromStepName($stepName)
    {
        return (int) array_search($stepName, $this->stepNames);
    }

    /**
     *
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     *
     * @return Step
     */
    public function current()
    {
        return $this->steps[$this->offset];
    }

    /**
     *
     * @return Step
     */
    public function next()
    {
        if (array_key_exists($this->offset+1, $this->steps)) {
            $this->offset++;
        }

        return $this;
    }

    /**
     *
     * @return Step
     */
    public function previous()
    {
        if (array_key_exists($this->offset-1, $this->steps)) {
            $this->offset--;
        }

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isFirstStep()
    {
        return 0 == $this->offset;
    }

    /**
     *
     * @return boolean
     */
    public function isLastStep()
    {
        return $this->offset == count($this->steps) -1;
    }

    /**
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->steps);
    }
}
