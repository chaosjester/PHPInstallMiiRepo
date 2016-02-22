# PHPInstallMiiRepo

This is a PHP front end to compile several files required by the InstallMii 3DS Homebrew app.

So far, this will only create your repo.list and the package.list files. In its current form it will also create a file called master.list with scraped details from all present SMDH files.  

The index has a download link to the repo.list, the package.list files are all for the backend.

Requirements:

Apache running on Linux

PHP shell_exec enabled (may not work for shared webhosts)

Directories must be writable

Ability to run shell commands on server (suPHP etc)

Instructions:

Modify the reposettings.php file to your liking

Upload to webhost

Go to http://yourrepo.com/repoupdate.php to kick off the update

Profit

TO DO:

Insert scraped data from SMDH in to packages.json to fully automate the repo.
