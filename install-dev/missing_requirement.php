<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="description" content="This store is powered by PrestaShop" />
  <style>
    ::-moz-selection {
      background: #b3d4fc;
      text-shadow: none;
    }

    ::selection {
      background: #b3d4fc;
      text-shadow: none;
    }

    html {
      padding: 30px 10px;
      font-size: 16px;
      line-height: 1.4;
      color: #737373;
      background: #f0f0f0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    html,
    input {
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    body {
      max-width:1000px;
      width: 1000px;
      padding: 30px 20px 50px;
      border: 1px solid #b3b3b3;
      border-radius: 4px;
      margin: 0 auto;
      box-shadow: 0 1px 10px #a7a7a7, inset 0 1px 0 #fff;
      background: #fcfcfc;
    }

    h1 {
      margin: 0 10px;
      font-size: 50px;
      text-align: center;
    }

    h1 span {
      color: #bbb;
    }
    h2 {
      color: #D35780;
      margin: 0 10px;
      font-size: 40px;
      text-align: center;
    }

    h2 span {
      color: #bbb;
      font-size: 60px;
    }

    h3 {
      margin: 1.5em 0 0.5em;
    }

    p {
      margin: 1em 0;
    }

    ul {
      padding: 0 0 0 40px;
      margin: 1em 0;
    }

    .container {
      max-width: 580px;
      width: 580px;
      margin: 0 auto;
    }

    .container p {
        text-align: center;
    }

    input::-moz-focus-inner {
      padding: 0;
      border: 0;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>We can't start installation :(</h2>

  <ol>
    <?php if (!extension_loaded('SimpleXML')): ?>
    <li>
        PrestaShop installation requires at least the <b>SimpleXML extension</b> to be enabled.
    </li>
    <?php endif; ?>
    <?php if (!extension_loaded('zip')): ?>
      <li>
          PrestaShop installation requires at least the <b>zip extension</b> to be enabled.
      </li>
    <?php endif; ?>
    <?php if (PHP_VERSION_ID < 50600): ?>
      <li>
          PrestaShop requires at least PHP 5.6 or newer versions.
          <i>You can install PrestaShop 1.6 if you can't update your version of PHP.</i>
      </li>
    <?php endif; ?>
        <?php if (!is_writable(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache')): ?>
      <li>
          PrestaShop installation needs to write critical files in the folder var/cache.
          <i>Please review the permissions on your server.</i>
      </li>
    <?php endif; ?>
  </ol>

  <p>You can contact your web host provider to fix theses requirements.</p>
</div>
</body>
</html>
