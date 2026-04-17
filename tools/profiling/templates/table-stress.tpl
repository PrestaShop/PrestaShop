{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="row">
	<h2><a name="tables">Tables stress</a></h2>
	<table class="table table-condensed">
    {foreach $tableStress as $table => $nb}
      <tr>
        <td>
          {table data=$nb} {$table}
        </td>
      </tr>
    {/foreach}
  </table>
</div>
