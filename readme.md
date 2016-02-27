# PHPInstallMiiRepo

This is a PHP front end to compile several files required by the InstallMii 3DS Homebrew app.

You can get InstallMii here - https://gbatemp.net/threads/wip-installmii-graphical-repository-downloader.406097/

This script will create your repo.list, package.list and scrape information from .smdh files to create the packages.json.

This script relies on there being an smdh file present in your homebrew application folders.  If one does not exist, there are tools to create one. If there is no smdh file, it will not be added.  Packages added manually will be removed when the script is run again.

The index has a download link to the repo.list, the package.list and packages.json files are all for the backend.

Requirements:

Homebrew apps MUST be in a folder named 3ds under the reporoot directory

Apache

suPHP. phpfcgi or other system that will execute the PHP as your account (may be required by shared host users)

Directories must be writable

All of the homebrew apps should be located in "<location of repoupdate.php>/3ds/". They will not be picked up otherwise

An smdh file must be present in the homebrew application folder or this will not pick it up

Instructions:

Modify the reposettings.php file to your liking

Upload to webhost

Go to http://yourrepo.com/repoupdate.php to kick off the update

Profit

If you want to automate this, you can set up a cron job (*nix) or schedulated task (Windows) "php /path/to/repoupdate.php" with your desired times
