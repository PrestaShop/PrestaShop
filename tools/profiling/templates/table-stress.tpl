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
