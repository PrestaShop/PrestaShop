<div class="row">
  <h2>
    <a name="objectModels">
      ObjectModel instances
    </a>
  </h2>

  <table class="table table-condensed">
    <thead>
      <tr>
        <th>Name</th>
        <th>Instances</th>
        <th>Source</th>
      </tr>
    </thead>

    <tbody>
      {foreach $objectmodel as $class => $info}
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
    </tbody>
  </table>
</div>
