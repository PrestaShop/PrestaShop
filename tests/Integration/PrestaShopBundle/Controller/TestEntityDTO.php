<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller;

use Tests\Integration\PrestaShopBundle\Controller\Exception\VariableNotFoundException;

class TestEntityDTO
{
    /** @var ?int */
    private $id;

    /**
     * @var array
     */
    private $variables;

    /**
     * Address constructor.
     *
     * @param int|null $id
     * @param array $variables
     */
    public function __construct(
        ?int $id,
        array $variables = []
    ) {
        $this->id = $id;
        $this->variables = $variables;
    }

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $variableName
     *
     * @return mixed
     *
     * @throws VariableNotFoundException
     */
    public function getVariable(string $variableName)
    {
        if (!isset($this->variables[$variableName])) {
            throw new VariableNotFoundException(sprintf('Variable %s not found in entity', $variableName));
        }

        return $this->variables[$variableName];
    }

    /**
     * @param string $variableName
     *
     * @return mixed
     *
     * @throws VariableNotFoundException
     */
    public function __get(string $variableName)
    {
        return $this->getVariable($variableName);
    }
}
