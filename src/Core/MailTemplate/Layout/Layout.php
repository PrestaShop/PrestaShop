<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

/**
 * Class MailLayout is the default implementation of MailLayoutInterface,
 * it is a simple immutable data container with no logic. It contains the
 * basic info about a mail layout which is used to generate a MailTemplate.
 */
class Layout implements LayoutInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $htmlPath;

    /** @var string */
    private $txtPath;

    /** @var string */
    private $moduleName;

    /**
     * @param string $name Name of the layout to describe its purpose
     * @param string $htmlPath Absolute path of the html layout file
     * @param string $txtPath Absolute path of the txt layout file
     * @param string $moduleName Which module this layout is associated to (if any)
     */
    public function __construct(
        $name,
        $htmlPath,
        $txtPath,
        $moduleName = ''
    ) {
        $this->name = $name;
        $this->htmlPath = $htmlPath;
        $this->txtPath = $txtPath;
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHtmlPath()
    {
        return $this->htmlPath;
    }

    /**
     * @return string
     */
    public function getTxtPath()
    {
        return $this->txtPath;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
