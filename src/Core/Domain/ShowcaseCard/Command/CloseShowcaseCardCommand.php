<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command;

use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\InvalidShowcaseCardNameException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\ShowcaseCardException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;

/**
 * This command permanently closes a showcase card
 */
class CloseShowcaseCardCommand
{
    /**
     * @var int
     */
    private $employeeId;

    /**
     * @var ShowcaseCard
     */
    private $showcaseCard;

    /**
     * CloseShowcaseCardCommand constructor.
     *
     * @param int $employeeId
     * @param string $showcaseCardName Name of the showcase card
     *
     * @throws InvalidShowcaseCardNameException
     * @throws ShowcaseCardException
     */
    public function __construct($employeeId, $showcaseCardName)
    {
        if (!is_int($employeeId)) {
            throw new ShowcaseCardException(sprintf('Expected employee id to be an int, but was %s', gettype($employeeId)));
        }

        $this->employeeId = $employeeId;
        $this->showcaseCard = new ShowcaseCard($showcaseCardName);
    }

    /**
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return ShowcaseCard
     */
    public function getShowcaseCard()
    {
        return $this->showcaseCard;
    }
}
