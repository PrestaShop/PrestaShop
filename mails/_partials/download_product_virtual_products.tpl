{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<ul>
{foreach $list as $product}
  <li><a href="{$product.url}">{$product.text}</a> {$product.complementary_text}</li>
{/foreach}
</ul>
