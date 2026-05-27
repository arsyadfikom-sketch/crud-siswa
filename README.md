sudo dnf install httpd -y
sudo systemctl enable httpd
sudo systemctl start httpd
sudo dnf install php php-cli php-common php-mysqlnd php-gd php-mbstring php-xml php-json php-opcache php-curl unzip -y
sudo dnf install mariadb105 -y
sudo dnf install mariadb105-server -y
sudo systemctl enable mariadb
sudo systemctl start mariadb
sudo mysql_secure_installation
sudo dnf install git -y
cd /var/www/html
sudo wget https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-all-languages.zip
sudo unzip phpMyAdmin-latest-all-languages.zip
sudo mv phpMyAdmin-*-all-languages phpMyAdmin
sudo chown -R apache:apache /var/www/html/phpMyAdmin

sudo nano /var/www/html/phpmyadmin/config.inc.php / 
sudo nano /etc/phpMyAdmin/config.inc.php
