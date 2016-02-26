<?php

require ("reposettings.php");

?>

<!DOCTYPE html>
<html>
<head>
  <!--Import Google Icon Font-->
  <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <link rel="stylesheet" type="text/css" href="custom.css">
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

</head>

<body>
  <br><br><br><br>
  <div class="container">
    <div class="row">
      <div class="col s12 m6 offset-m3 center-align">
        <div class="card">
          <span class="card-title"><?php echo $reponame ?></span>
          <div class="card-content">
            <p>Welcome to my 3DS InstallMii repo.<br>Please download my repo.list and place on your 3DS' SD card in your InstallMii directory.</p>
          </div>
          <div class="card-action">
            <a href="repo.list" download>Download repo.list</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="page-footer">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Repo Provided by <?php echo $repoowner ?></h5>
          <p class="grey-text text-lighten-4"><?php echo $repoblurb ?></p>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Created with ChaosJester's PHP InstallMii Repo creator<br>Big thanks to LiquidFenrir for his assistance!
      <a class="grey-text text-lighten-4 right" href="https://github.com/chaosjester/PHPInstallMiiRepo" target="_blank">Project GitHub page</a>
      </div>
    </div>
  </footer>



  <!--Import jQuery before materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>
