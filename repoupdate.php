<?php

require ('reposettings.php');

// Create repo.list
$repofile = '{"repos":[{"name":"'.$reponame.'","url":"'.$repourl.'"}]}';
file_put_contents($reporoot.'/repo.list', $repofile);

// Create Shell Script to create package.list
$packagefile = 'for d in '.$repodir.'/*; do cd ${d} && rm -f package.* && rm -f *.smdh.txt && find . -type f | sed s,^./,, > package.tmp && sed "/package.tmp/d" package.tmp > package.list && rm package.tmp  && cd ..; done';
file_put_contents($repodir.'/packagelistgen.sh', $packagefile);

// Create SMDH Scraper
unlink('master.list');
$scrapefile = 'for f in $(find '.$repodir.' -name "*.smdh"); do printf "name = " > $f.txt && dd if=$f ibs=1 skip=08 count=80 >> $f.txt && printf "\nshort_description = " >> $f.txt && dd if=$f ibs=1 skip=136 count=200 >> $f.txt && printf "\nauthor = " >> $f.txt && dd if=$f ibs=1 skip=392 count=80 >> $f.txt && path=$f && printf "\ncategory = nul\nwebsite = nul\ntype = 3ds\nversion = nul\n" >> $f.txt && echo "dl_path = nul\ninfo_path = ${path#'.$reporoot.'}" >> $f.txt && tr -d "\0" < $f.txt >> '.$reporoot.'/master.list && rm -f $f.txt; done';
file_put_contents($repodir.'/scrapesmdh.sh', $scrapefile);

// Run shell script to create package.list and scrape SMDH Files
shell_exec('chmod +x '.$repodir.'/packagelistgen.sh');
shell_exec('chmod +x '.$repodir.'/scrapesmdh.sh');
shell_exec($repodir.'/packagelistgen.sh');
shell_exec($repodir.'/scrapesmdh.sh');

$dir = "./3ds/";
$apps = array_diff(scandir($dir), array('..', '.', 'packagelistgen.sh', 'scrapesmdh.sh'));
sort($apps);
$flipped = array_flip($apps);
$dl_path = $apps;
foreach ($dl_path as &$item) { $item = "3ds/".$item."/"; }
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
<div class="container">
<?php
// Big thanks to LiquidFenrir for his assistance (by assistance I mean writing all the code below!)
$repoInfo = array();
$repoInfo["name"] = $reponame;
$repoInfo["author"] = $repoowner;
$repoInfo["website"] = $repourl;

$fields = array(); // dont comment that one out, will break everything else
$fields[] = "name";
$fields[] = "short_description";
$fields[] = "author";
$fields[] = "category";
$fields[] = "website";
$fields[] = "type";
$fields[] = "version";
$fields[] = "dl_path";
$fields[] = "info_path";

$lines = file("master.list", FILE_IGNORE_NEW_LINES);
$i = 1;
for ($i;$i <= sizeof($apps);$i++) {
  if (strpos($lines[$i*9-1], 'info_path = ') !== false) {
    $id = explode("/",$lines[$i*9-1]);
    $lines[$i*9-2] = "dl_path = ".$dl_path[$flipped[$id[2]]];
    $lines[$i*9-1] = substr($lines[$i*9-1],1);
  }
}
$list = array_chunk($lines, sizeof($fields)); // cut the array that contained the lines into pieces to separate each app
$size = sizeof($list)-1;

$i = 0;
for($i;$i <= $size;$i++) { // change the name of keys in the array to make the json compatible with installMii
  $j = 0;
  for($j;$j <= sizeof($fields)-1;$j++) {
    $list[$i][$fields[$j]] = substr($list[$i][$j],strlen($fields[$j])+3); // remove the tags to only have the value
    if ($list[$i][$fields[$j]] == "nul") { $list[$i][$fields[$j]] = null;}
    unset($list[$i][$j]); // remove the old key that is not needed anymore and only takes space (and would mess with json_encode)
  }
}

// create a new array before encoding to get the "repo" and "packages" part correctly
$formattedjson = json_encode(array("repo"=>$repoInfo, "packages"=>$list));
$formattedjson = str_replace("\/","/",$formattedjson);
if (!$formattedjson){
	echo '<div class="col s12 m6 offset-m3">
          <div class="card red darken-1 center-align">
            <div class="card-content white-text">
              <span class="card-title">packages.json creation failed</span>
              <p>Please refer to your webserver error log for more information</p>
            </div>
          </div>
        </div>';
} else {
echo ' <div class="col s12 m6 offset-m3">
          <div class="card green darken-1 center-align">
            <div class="card-content white-text">
              <span class="card-title">Successfully added to packages.json</span>
              <p>'.$formattedjson.'</p>
            </div>
          </div>
        </div>';

file_put_contents('./packages.json', $formattedjson);
};
 ?>

 </div>

  <!--Import jQuery before materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>
