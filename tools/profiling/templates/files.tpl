<div class="row">
  <h2>
    <a name="includedFiles">
      Included Files
    </a>
  </h2>

  <table class="table table-condensed">
    <tr>
      <th>#</th>
      <th>Filename</th>
    </tr>
    {foreach $files as $i => $file}
      {$file = str_replace('\\', '/', str_replace(_PS_ROOT_DIR_, '', $file))}
      {if (strpos($file, '/tools/profiling/') !== 0)}
        <tr>
          <td>
            {$i}
          </td>
          <td>
            {$file}
          </td>
        </tr>
      {/if}
    {/foreach}
  </table>
</div>
