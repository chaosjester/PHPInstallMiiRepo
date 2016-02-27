<?php

require ('reposettings.php');

// Create repo.list
$repofile = '{"repos":[{"name":"'.$reponame.'","url":"'.$repourl.'"}]}';
file_put_contents('./repo.list', $repofile);

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
$apps = array_diff(scandir("./3ds/"), array('..', '.')); // getting all the apps in the 3ds folder
sort($apps);
$dl_path = $apps;
$info_path = $apps;
foreach ($dl_path as &$item) { $item = "3ds/".$item."/"; }
foreach ($info_path as &$item) { $item = "3ds/".$item."/".$item.".smdh"; }

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

$i = 0;
for($i;$i < sizeof($apps);$i++) { // removing entries from the array if they don't have a .smdh in their folder or are files
  if(!is_dir(substr($dl_path[$i],0,-1)) || !file_exists($info_path[$i])) {
    unset($apps[$i]);
    unset($info_path[$i]);
    unset($dl_path[$i]);
  }
}
sort($apps);
sort($dl_path);
sort($info_path);

// The 2 following functions were posted by frasq@frasq.org here: http://php.net/manual/en/function.readdir.php#100710
function listdir($dir='.') {
  $files = array();
  listdiraux($dir, $files);
  return $files;
}

function listdiraux($dir, &$files) {
  $handle = opendir($dir);
  while (($file = readdir($handle)) !== false) {
    if ($file == '.' || $file == '..') {
      continue;
    }
    $filepath = $dir == '.' ? $file : $dir . '/' . $file;
    if (is_link($filepath))
      continue;
    if (is_file($filepath))
      if(strpos($filepath,"package.list") === false) {
        $files[] = $filepath;
      } else {
        continue;
      }
    else if (is_dir($filepath))
      listdiraux($filepath, $files);
  }
  closedir($handle);
}

$i=0;
for($i;$i < sizeof($apps);$i++) {
  $files = listdir(substr($dl_path[$i],0,-1));
  sort($files, SORT_LOCALE_STRING);
  file_put_contents($dl_path[$i]."package.list",str_replace($dl_path[$i],"",implode("\n", $files)));
}

$list = array();
$i = 0;
for ($i;$i <= sizeof($apps)-1;$i++) { // scrape the SMDH files
  $list[$i] = array();
  $file = file_get_contents($info_path[$i]);
  $list[$i][$fields[0]] = substr($file,8,80); //reading the parts with the name, desc and author in the smdh
  $list[$i][$fields[1]] = substr($file,392,80);
  $list[$i][$fields[2]] = substr($file,136,100);
  // those null parts are where the master.list idea can be better than using php to read the smdh, but I don't think there's a zone for version in a smdh
  $list[$i][$fields[3]] = null;
  $list[$i][$fields[4]] = null;
  $list[$i][$fields[5]] = "3ds"; // to make installMii install in the sd:/3ds/ folder
  $list[$i][$fields[6]] = null;
  $list[$i][$fields[7]] = $dl_path[$i];
  $list[$i][$fields[8]] = $info_path[$i];
}

// create a new array before encoding to get the "repo" and "packages" part correctly
$formattedjson = json_encode(array("repo"=>$repoInfo, "packages"=>$list));
// somehow using php to read the smdh pads every letter with a null byte
// I don't know how to get rid of it in the input, but we can simply remove it in the output
// could replace the str_replace() for escaped slashes by JSON_UNESCAPED_SLASHES in the json_encode() but requires at least php 5.4
$formattedjson = str_replace("\u0000","",str_replace("\/","/",$formattedjson));

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
            <div class="card-content white-text left-align">
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
