<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;

/**
 * Class Name
 */
class Name
{
    /**
     * @var string
     */
    private $pageName;

    /**
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    public function __construct($pageName)
    {
        $this->assertIsValidPageName($pageName);

        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    private function assertIsValidPageName($pageName)
    {
        if (!is_string($pageName) || !$pageName || !preg_match('/^[a-zA-Z0-9_.-]+$/', $pageName)) {
            throw new MetaConstraintException(sprintf('Invalid Meta page name %s', var_export($pageName, true)), MetaConstraintException::INVALID_PAGE_NAME);
        }
    }
}
