<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Holds the abstraction required for Adding or updating the cms page category.
 */
abstract class AbstractCmsPageCategoryHandler extends AbstractObjectModelHandler
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $localisedTexts
     *
     * @return bool
     */
    protected function assertHasDefaultLanguage(array $localisedTexts)
    {
        $errors = $this->validator->validate($localisedTexts, new DefaultLanguage());

        return 0 === count($errors);
    }

    /**
     * @param array $localisedUrls
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertIsValidLinkRewrite(array $localisedUrls)
    {
        foreach ($localisedUrls as $localisedUrl) {
            $errors = $this->validator->validate($localisedUrl, new IsUrlRewrite());

            if (0 !== count($errors)) {
                throw new CmsPageCategoryConstraintException(sprintf('Given friendly url "%s" is not valid for link rewrite', $localisedUrl), CmsPageCategoryConstraintException::INVALID_LINK_REWRITE);
            }
        }
    }

    /**
     * @param array $localisedDescription
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertDescriptionContainsCleanHtml(array $localisedDescription)
    {
        foreach ($localisedDescription as $description) {
            $errors = $this->validator->validate($description, new CleanHtml());

            if (0 !== count($errors)) {
                throw new CmsPageCategoryConstraintException(sprintf('Given description "%s" contains javascript events or script tags', $description), CmsPageCategoryConstraintException::INVALID_DESCRIPTION);
            }
        }
    }
}
