<style>
  #prestashop-profiling {
    padding: 20px;
  }

  .ps_back-office.page-sidebar #prestashop-profiling {
    margin-left: 210px;
  }
  .ps_back-office.page-sidebar-closed #prestashop-profiling {
    margin-left: 50px;
  }
  .ps_back-office #prestashop-profiling {
    clear: both;
    padding: 10px;
    margin-bottom: 50px;
  }

  #prestashop-profiling * {
    box-sizing:border-box;
    -moz-box-sizing:border-box;
  }

  #prestashop-profiling .table {
    width: 100%;
  }

  #prestashop-profiling .table td,
  #prestashop-profiling .table th {
    padding: 6.4px;
    padding: .4rem;
    vertical-align: top;
    border-top: 1px solid #bbcdd2;
    vertical-align: middle;
    text-align: left;
  }
  #prestashop-profiling .table th {
    padding-top: 0.625rem;
    padding-bottom: 0.625rem;
  }
  #prestashop-profiling .table thead th {
    border-bottom: .125rem solid #25b9d7;
  }

  #prestashop-profiling .table tfoot th {
    border-top: .125rem solid #25b9d7;
  }

  #prestashop-profiling .sortable thead th {
    cursor:pointer;
  }

  #prestashop-profiling .table td .pre {
    padding: 6px;
    margin-right: 10px;
    overflow: auto;
    display: block;
    color: #777;
    font-size: 12px;
    line-height: 1.42857;
    word-break: break-all;
    word-wrap: break-word;
    background-color: whitesmoke;
    border: 1px solid #cccccc;
    max-width: 960px;
  }

  #prestashop-profiling .row {
    clear: both;
    margin-bottom: 20px;
  }

  #prestashop-profiling .col-4 {
    float: left;
    padding: 0 10px;
    width: 33%;
  }

  @media (max-width: 1200px) {
    #prestashop-profiling .col-4 {
      width: 50%;
    }
  }
  @media (max-width: 600px) {
    #prestashop-profiling .col-4 {
      width: 100%;
    }
  }

  .success {
    color: green;
  }
  .danger {
    color: red;
  }
  .warning {
    color: #EF8B00;
  }
</style>

<script type="text/javascript" src="https://cdn.rawgit.com/drvic10k/bootstrap-sortable/1.11.2/Scripts/bootstrap-sortable.js"></script>
