<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DisableableLinkColumn extends AbstractColumn
{
    /**
     * @var LinkColumn
     */
    private $linkColumn;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->linkColumn = new LinkColumn($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'disableable_link';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['disabled_field'])
            ->setAllowedTypes('disabled_field', ['string', 'null'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $disabledOptions = [];

        if (isset($options['disabled_field'])) {
            $disabledOptions['disabled_field'] = $options['disabled_field'];
            unset($options['disabled_field']);
        }

        $this->linkColumn->setOptions($options);
        parent::setOptions($disabledOptions);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return array_merge($this->linkColumn->getOptions(), parent::getOptions());
    }
}
