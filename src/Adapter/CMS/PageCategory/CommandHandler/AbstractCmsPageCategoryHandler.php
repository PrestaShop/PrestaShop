<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
                throw new CmsPageCategoryConstraintException(
                    sprintf(
                        'Given friendly url "%s" is not valid for link rewrite',
                        $localisedUrl
                    ),
                    CmsPageCategoryConstraintException::INVALID_LINK_REWRITE
                );
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
                throw new CmsPageCategoryConstraintException(
                    sprintf(
                        'Given description "%s" contains javascript events or script tags',
                        $description
                    ),
                    CmsPageCategoryConstraintException::INVALID_DESCRIPTION
                );
            }
        }
    }
}
