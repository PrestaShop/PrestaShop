<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\InvalidException;

class MailTemplateCollection implements MailTemplateCollectionInterface
{
    /** @var MailTemplateInterface[] */
    private $templates;

    /**
     * @param array $templates
     *
     * @throws InvalidException
     */
    public function __construct(array $templates = [])
    {
        $this->setTemplates($templates);
    }

    /**
     * {@inheritdoc}
     */
    public function has(MailTemplateInterface $template)
    {
        return false !== array_search($template, $this->templates);
    }

    /**
     * {@inheritdoc}
     */
    public function add(MailTemplateInterface $template)
    {
        if (!$this->has($template)) {
            $this->templates[] = $template;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(MailTemplateInterface $template)
    {
        $index = array_search($template, $this->templates);
        if (false === $index) {
            throw new InvalidException('Can not remove absent element from collection');
        }

        array_splice($this->templates, $index, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplates($templates = [])
    {
        foreach ($templates as $template) {
            if (!($template instanceof MailTemplateInterface)) {
                throw new InvalidException(sprintf('Invalid argument of type %s in array, %s expected', get_class($template), MailTemplateInterface::class));
            }
        }

        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->templates);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->templates);
    }
}
