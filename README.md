
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

** enable mongo to php **

sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

sudo apt-get install php-pear
sudo pecl install mongodb
****

create directories
json/SFT/vinter
excel-surveys/SFT


** generate a ssh key for apache user **
sudo -u www-data ssh-keygen -t rsa
double check existence 
ls -la /var/www/.ssh/id_rsa
add server destination to known_hosts
/var/www/.ssh/known_hosts
add server's public key (/var/www/.ssh/id_rsa.pub) to destination server file authorized_keys

