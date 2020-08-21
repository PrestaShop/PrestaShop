<div class="row">
  <h2>
    <a name="doubles">Doubles</a>
  </h2>

  <table class="table table-condensed">
    {foreach $doublesQueries as $q => $nb}
      {if ($nb > 1)}
        <tr>
          <td>
            {sql_queries data=$nb}
          </td>
          <td class="pre">
            <pre>{$q}</pre>
          </td>
        </tr>
      {/if}
    {/foreach}
  </table>
</div>
