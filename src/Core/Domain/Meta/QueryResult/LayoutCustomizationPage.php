<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult;

/**
 * Class LayoutCustomizationPage.
 */
class LayoutCustomizationPage
{
    /**
     * @var string
     */
    private $page;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $page
     * @param string $title
     * @param string $description
     */
    public function __construct($page, $title, $description)
    {
        $this->page = $page;
        $this->description = $description;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
