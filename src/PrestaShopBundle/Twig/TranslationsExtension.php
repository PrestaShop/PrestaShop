<?php
/**
 * 2007-2016 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Twig;

class TranslationsExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    public $translator;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getTranslationsTree', array($this, 'getTranslationsTree')),
        );
    }

    /**
     * Returns a tree of translations key values.
     *
     * @param array $translationsTree
     * @return string
     */
    public function getTranslationsTree(array $translationsTree)
    {
        $output = '';
        end($translationsTree);
        list($lastTranslationDomain) = each($translationTree);
        reset($translationTree);

        foreach ($translationsTree as $topLevelDomain => $tree) {
            $output .= $this->concatenateSubtreeHeader($topLevelDomain, $tree);

            if ($lastTranslationDomain !== $topLevelDomain) {
                $output .= '<hr />';
            }
        }

        return $output;
    }

    /**
     * @param $tree
     * @return string|\Symfony\Component\Translation\MessageCatalogue
     */
    public function makeSubtree($tree, $level = 3)
    {
        $output = '';
        $messagesSubtree = $this->hasCamelizedDomain($tree);

        if ($messagesSubtree) {
            $camelizedDomain = $tree['__camelized_domain'];
            unset($tree['__camelized_domain']);

            $inputTemplateParts = array(
                '<form method="post" class="hide" action="edit">',
                '    <div class="alerts">',
                '        <div class="hide alert alert-info">Translation successfully edited</div>',
                '        <div class="hide alert alert-danger">Translation unsuccessfully edited</div>',
                '    </div>',
                '    <p><strong>Translation key:</strong> <verbatim>{key}</verbatim></p>',
                '    <div class="form-group row">',
                '        <div class="col-lg-12">',
                '            <textarea class="form-control" rows="3" name="translation_value">{edited_translation_value}</textarea>',
                '        </div>',
                '        <input type="hidden" name="domain" value="{domain}"/>',
                '        <input type="hidden" name="locale" value="{locale}"/>',
                '        <input type="hidden" name="default" value="{default_translation_value}"/>',
                '        <input type="hidden" name="translation_key" value="{key}"/>',
                '    </div>',
                '    <div class="col-md-offset-1">',
                '        <input class="btn btn-default btn-sm" type="submit" value="{edit}"/>',
                '        <input class="btn btn-default btn-sm reset-translation-value" type="button" value="{reset}"/>',
                '    </div>',
                '    <hr/>',
                '</form>',
            );
            $inputTemplate = implode('', $inputTemplateParts);

            $editAction = $this->translator->trans('Edit', array(), 'AdminActions', 'en-US');
            $resetAction = $this->translator->trans('Reset', array(), 'AdminActions', 'en-US');

            $formIndex = 0;
            $pageIndex = 1;
            $itemsPerPage = 25;
            $output .= '<div class="page" data-status="active" data-page-index="1">';

            foreach ($tree as $translationKey => $translationValue) {
                list($domain, $locale) = explode('.', $camelizedDomain);
                $defaultTranslationValue = $this->translator->trans($translationKey, array(), $domain, $locale);

                // Extract default translation value from xliff files for reset
                if (is_array($translationValue)) {
                    $defaultTranslationValue = $translationValue['xlf'];
                    $translationValue = $translationValue['db'];
                }

                $output .= str_replace(
                    array(
                        '{key}',
                        '{domain}',
                        '{locale}',
                        '{default_translation_value}',
                        '{edited_translation_value}',
                        '{edit}',
                        '{reset}'
                    ),
                    array(
                        $translationKey,
                        $domain,
                        $locale,
                        $defaultTranslationValue,
                        $translationValue,
                        $editAction,
                        $resetAction
                    ),
                    $inputTemplate
                );

                $isLastPage = $formIndex + 1 === count($tree);

                if ($isLastPage) {
                    $output .= '</div>';
                } elseif ((0 === $formIndex % $itemsPerPage) && ($formIndex > 0)) {
                    $pageIndex++;

                    // Close div with page class
                    $output .= '</div>';
                    $output .= '<div class="page hide" data-status="inactive" data-page-index="' . $pageIndex . '">';
                }

                $formIndex++;
            }

            // Close div with page class when no message is available
            if (count($tree) === 0) {
                $output .= '</div>';
            }
        } else {
            foreach ($tree as $subdomain => $subtree) {
                $output .= $this->concatenateSubtreeHeader($subdomain, $subtree, $level);
            }
        }

        return $output;
    }

    /**
     * @param $tree
     * @return bool
     */
    protected function hasCamelizedDomain($tree)
    {
        return array_key_exists('__camelized_domain', $tree);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_translations_extension';
    }

    /**
     * @param $subdomain
     * @param $subtree
     * @param int $level
     * @return string
     */
    protected function concatenateSubtreeHeader($subdomain, $subtree, $level = 2)
    {
        $messagesSubtree = $this->hasCamelizedDomain($subtree);
        $subject = $this->makeSubdomainPrefix($level) . $subdomain;

        $id = null;
        if ($messagesSubtree) {
            $id = $this->parseDomain($subtree);
        }

        $output = $this->tagSubject($subject, $level, $id);

        if ($messagesSubtree) {
            $output .= implode(array(
                '<div class="translation-domain">',
                '    <button class="btn btn-default btn-sm show-translation-messages">Show messages</button>',
                '    <button class="btn btn-default btn-sm hide hide-translation-messages">Hide messages</button>',
            ));

            $output .= $this->getNavigation($this->parseDomain($subtree));
        }

        $output .= '<div class="col-offset">';
        $output .= $this->makeSubtree($subtree, $level + 1);
        $output .= '</div>';

        if ($messagesSubtree) {
            $output .= '<a href="#_' . $id . '" class="hide btn btn-sm btn-default go-to-domain-menu">' .
                'Go to previous navigation menu' .
                '</a>'
            ;

            // Close div with translation-domain class
            $output .= '</div>';
        }

        return $output;
    }

    /**
     * @param $subtree
     * @return mixed
     */
    protected function parseDomain($subtree)
    {
        list($domain) = explode('.', $subtree['__camelized_domain']);

        return $domain;
    }

    /**
     * @param $id
     * @return string
     */
    protected function getNavigation($id) {
        return implode(array(
            '<nav class="hide">',
            '    <ul class="pagination">',
            '        <li class="page-item active" data-page-index="1"><a class="page-link" href="#_' . $id . '">1</a></li>',
            '        <li class="page-item tpl hide"><a class="page-link" href="#_' . $id . '">2</a></li>',
            '    </ul>',
            '</nav>',
        ));
    }

    /**
     * @param $level
     * @return string
     */
    protected function makeSubdomainPrefix($level)
    {
        $subdomainPrefix = '';
        if ($level > 1) {
            $subdomainPrefix = '<span class="separator">' . str_repeat(' > ', $level - 1) . ' </span>';
        }

        return $subdomainPrefix;
    }

    /**
     * @param $subject
     * @param $level
     * @param null $id
     * @return string
     */
    protected function tagSubject($subject, $level, $id = null)
    {
        $openingTag = '';
        $closingTag = ' ';

        if (2 === $level) {
            $openingTag = '<h2>';
            $closingTag = '</h2>';
        }

        if ($id) {
            $openingTag = '<span id="_' . $id . '">';
            $closingTag = '</span>';
        }

        return $openingTag . $subject . $closingTag;
    }
}
