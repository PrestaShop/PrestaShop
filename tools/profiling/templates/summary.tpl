<div class="col-4">
  <table class="table table-condensed">
    <tr>
      <td>
        Load Time
      </td>
      <td>
        {$summary.loadTime}
      </td>
    </tr>
    <tr>
      <td>Querying Time</td>
      <td>
        {total_querying_time data=$summary.queryTime} ms
      </td>
    </tr>
    <tr>
      <td>
        Queries
      </td>
      <td>
        {total_queries data=$summary.nbQueries}
      </td>
    </tr>
    <tr>
      <td>
        Memory Peak Usage
      </td>
      <td>
        {peak_memory data=$summary.peakMemoryUsage}
      </td>
    </tr>
    <tr>
      <td>
        Included Files
      </td>
      <td>
        {$summary.includedFiles} files - {memory data=$summary.totalFileSize}
      </td>
    </tr>
    <tr>
      <td>
        PrestaShop Cache
      </td>
      <td>
        {memory data=$summary.totalCacheSize}
      </td>
    </tr>
    <tr>
      <td>
        <a href="javascript:void(0);" onclick="$('.global_vars_detail').toggle();">Global vars</a>
      </td>
      <td>
        {memory data=$summary.totalGlobalVarSize}
      </td>
    </tr>

    {foreach $summary.globalVarSize as $global=>$size}
      <tr class="global_vars_detail" style="display:none">
        <td>
          - global ${$global}
        </td>
        <td>
          {$size}k
        </td>
      </tr>
    {/foreach}
  </table>
</div>
