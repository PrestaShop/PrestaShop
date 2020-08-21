<div class="row">
  <h2>
    <a name="objectModels">
      ObjectModel instances
    </a>
  </h2>

  <table class="table table-condensed">
    <tr>
      <th>Name</th>
      <th>Instances</th>
      <th>Source</th>
    </tr>
    {foreach $objectmodel.classes as $class => $info}
      <tr>
        <td>{$class}</td>
        <td>
          {objectmodel data=count($info)}
        </td>
        <td>
        {foreach $info as $trace}
          {str_replace([_PS_ROOT_DIR_, '\\'], ['', '/'], $trace['file'])} [{$trace['line']}]
          <br />
        {/foreach}
        </td>
      </tr>
    {/foreach}
  </table>
</div>
