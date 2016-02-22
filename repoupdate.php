<?php 

require ('reposettings.php');

// Create repo.list
$repofile = '{"repos":[{"name":"'.$reponame.'","url":"'.$repourl.'"}]}';
file_put_contents($reporoot.'/repo.list', $repofile);

// Create Shell Script to create package.list
$packagefile = 'for d in '.$repodir.'/*; do cd ${d} && rm -f package.* && find . -type f | sed s,^./,, > package.tmp && sed "/package.tmp/d" package.tmp > package.list && rm package.tmp  && cd ..; done';
file_put_contents($repodir.'/packagelistgen.sh', $packagefile);

// Create SMDH Scraper
$scrapefile = 'for f in $(find '.$repodir.' -name "*.smdh"); do dd if=$f ibs=1 skip=08 count=80 > $f.txt && printf "\n" >> $f.txt && dd if=$f ibs=1 skip=136 count=200 >> $f.txt && printf "\n" >> $f.txt && dd if=$f ibs=1 skip=392 count=80 >> $f.txt && printf "\n" >> $f.txt && tr -d "\0" < $f.txt >> '.$repodir.'/master.list && rm -f $f.txt; done';
file_put_contents($repodir.'/scrapesmdh.sh', $scrapefile);

// Run shell script to create package.list and scrape SMDH Files
shell_exec('chmod +x '.$repodir.'/packagelistgen.sh');
shell_exec('chmod +x '.$repodir.'/scrapesmdh.sh');
shell_exec($repodir.'/packagelistgen.sh');
shell_exec($repodir.'/scrapesmdh.sh');

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


  <!--Import jQuery before materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>