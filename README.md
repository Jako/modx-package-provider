#One file implementation of MODX Package Provider

This repository contains a very basic MODX Package Provider, that uses a config file for each package and shows the latest package version of each package for download.

A package config file contains a comma separated user list to restrict package visibility for that user. It is not secured by an API key. A sample package and config file is available in _packages folder. It just installs a category + namespace and a folder.

This PHP script requires a Apache2.2/PHP installation. Tested with PHP 5.3/5.4.

## Installation

- Copy the files and folders to your web server and rename `ht.access` to `.htaccess` in that folder and modify it to suit your needs (e.g. change RewriteBase to a subfolder).
- Edit line 131 in `index.php` and insert the full url to your package provider. If you call this url, a basic information page is shown showing a few links to test your package provider functionality. All links should return an xml answer.
- If you want to log the usage of the Package Provider, make the `_log` folder writable, rename  `ht.access` to `.htaccess` in that folder and change line 132 in `index.php` to `$debug = true;`.

## Add the Package Provider in MODX Manager

- Select `Extras -> Installer -> Providers -> Add New Provider`
- In Name set `Your repo name`
- In Service URL set the URL to the directory with index.php, e.g. `http://your.packageprovider.url/extras/`
- After that you can choose on Packages tab, `Download Extras -> Select a provider -> Your repo name`
