<?php

require ('reposettings.php');

// Create repo.list
$repofile = '{"repos":[{"name":"'.$reponame.'","url":"'.$repourl.'"}]}';
file_put_contents($reporoot.'/repo.list', $repofile);

// Create Shell Script to create package.list
$packagefile = 'for d in '.$repodir.'/*; do cd ${d} && rm -f package.* && find . -type f | sed s,^./,, > package.tmp && sed "/package.tmp/d" package.tmp > package.list && rm package.tmp  && cd ..; done';
file_put_contents($repodir.'/packagelistgen.sh', $packagefile);

// Create SMDH Scraper
unlink('master.list');
$scrapefile = 'for f in $(find '.$repodir.' -name "*.smdh"); do printf "name = " > $f.txt && dd if=$f ibs=1 skip=08 count=80 >> $f.txt && printf "\nshort_description = " >> $f.txt && dd if=$f ibs=1 skip=136 count=200 >> $f.txt && printf "\nauthor = " >> $f.txt && dd if=$f ibs=1 skip=392 count=80 >> $f.txt && printf "\n" >> $f.txt && tr -d "\0" < $f.txt >> '.$reporoot.'/master.list; done';
file_put_contents($repodir.'/scrapesmdh.sh', $scrapefile);

// Run shell script to create package.list and scrape SMDH Files
shell_exec('chmod +x '.$repodir.'/packagelistgen.sh');
shell_exec('chmod +x '.$repodir.'/scrapesmdh.sh');
shell_exec($repodir.'/packagelistgen.sh');
shell_exec($repodir.'/scrapesmdh.sh');

$smdhfile = file('master.list');
$smdhinfo = str_replace (array("\r\n", "\n", "\r"), '', $smdhfile);


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
// (2016-02-24, morning utc+1)
// SO, this new version brings: repo info, "repo" and "packages" fields in the json, and is fully compatible with the bigger one
// (2016-02-24, 02:30 AM utc+1)
// this even newer versions changes the keys all by itself, depending on the "fields" array. change the array as you change your scrapper!

// I wouldn't really care about the increased RAM usage of making another array at then if I were you, it's most probably negligeable.

// change these as you like! they are what define your repo for installMii. Could even read them from a settings/config file

addcslashes($repourl, '/');
$repoInfo = array();
$repoInfo["name"] = $reponame;
$repoInfo["author"] = $repoowner;
$repoInfo["website"] = $repourl;

// comment out/in the fields as you adapt your scraper!
$fields = array(); // dont comment that one out, will break everything else
$fields[] = "name";
$fields[] = "short_description";
$fields[] = "author";
// those are commented out because your scraper doesnt put them in the master.list yet
// $fields[] = "category";
// $fields[] = "website";
// $fields[] = "type";
// $fields[] = "version";
$fields[] = "dl_path";
$fields[] = "info_path";



$lines = array();
$file = fopen("master.list", "r");
if($file) {
    while(!feof($file)){
        $line = substr(fgets($file),0,-1);
        $lines[] = $line;
    }
}
fclose($file);

array_pop($lines);
$list = array_chunk($lines, 3); // cut the array that contained the lines into pieces to separate each app
$size = sizeof($list)-1;

$i = 0;
for($i;$i <= $size;$i++) { // change the name of keys in the array to make the json compatible with installMii
  $j = 0;
  for($j;$j <= sizeof($fields)-1;$j++) {
    $list[$i][$fields[$j]] = substr($list[$i][$j],strlen($fields[$j])+3); // remove the "name = ", and things like that to only have the value ("3dscraft", or anything)
    unset($list[$i][$j]);// remove the old key that is not needed anymore and only take space (and would mess with json_encode)
  }
}

// create a new array before encoding to get the "repo" and "packages" part correctly
$formattedjson = json_encode(array("repo"=>$repoInfo, "packages"=>$list));

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

file_put_contents($reporoot.'/packages.json', $formattedjson);
};
 ?>

 </div>

  <!--Import jQuery before materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>