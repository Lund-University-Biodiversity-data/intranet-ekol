
** EXCEL SURVEYS IMPORTER **
Console mode 
´´´
php transform_excel_json.php DEV std 2 debug
´´´

**Install**
 - install the repo Lund-University-Biodiversity-data/shared-functions.git
 - rename config.template.php in config.php, and change the folders paths
 

#bootstrap table 
npm install bootstrap-table

** transform excel forms to json **
sudo apt install composer
sudo apt-get install php-mbstring
composer require phpoffice/phpspreadsheet

** for reading XLSX file
sudo apt-get install php7.4-xml
sudo apt-get install php8.2-xml

** enable mongo to php **


sudo apt-get install php8.2-mongodb


sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

sudo apt-get install php-pear
sudo pecl install mongodb


if 
sh: 1: phpize: not found
ERROR: `phpize' failed

=> 
sudo apt install php-dev

****

create directories
json/SFT/vinter (with writing rights)
excel-surveys/SFT
excel-coordinates/SFT
csv/stdRecapComments (with writing rights)
csv/stdCentroidStdCoord (with writing rights)
csv/stdCentroidTopokartan (with writing rights)
csv/surveyorsYears (with writing rights)

** generate a ssh key for apache user **
sudo -u www-data ssh-keygen -t rsa
double check existence 
ls -la /var/www/.ssh/id_rsa
add server destination to known_hosts
/var/www/.ssh/known_hosts
add server's public key (/var/www/.ssh/id_rsa.pub) to destination server file authorized_keys

