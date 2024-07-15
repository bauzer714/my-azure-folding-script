#!/usr/bin/env bash

echo "=========Add the self-service website================";
sudo add-apt-repository ppa:ondrej/php -y;
echo "=========Attempting to update and upgrade================";
sudo apt-get -y update;
sudo apt-get -y upgrade;
echo "=========Installing dependencies================";
sudo apt-get -y install bzip2;
echo "=========Installing the website self-service dependencies======";
sudo apt-get install -y nginx;
sudo apt-get install -y php8.3-fpm;
echo "=========Show working directory================";
pwd;
echo "=========Build up the website=============";
echo "--- nginx config and restart";
sudo cp -f ./self-service-web/.config/default /etc/nginx/sites-available/default;
sudo service nginx restart;
echo "--- copying files to web dir";
sudo cp -rf ./self-service-web/* /var/www/html;
#DISABLE IF SERVER IS PUBLICLY ACCESSIBLE
echo "--- giving access to reboot the server";
echo '%www-data ALL=NOPASSWD: /sbin/shutdown' | sudo tee -a /etc/sudoers;
echo "=========Clean up working directory================";
rm -rf runner;
mkdir runner;
cd runner;
pwd;
echo "=========Download fahclient================";
wget https://download.foldingathome.org/releases/public/fah-client/debian-10-64bit/release/latest.tar.bz2
echo "=========Prepare subdirectories================";
mkdir fclient;
tar jxf lat*;
cp -r fah*/* fclient;
cd fclient;
echo "=========Build config================";
echo '<config>' > config.xml;
echo '  <user value="bauzer714"/> <!-- Enter your user name here -->' >> config.xml;
echo '  <team value="47180"/>         <!-- Your team number -->' >> config.xml;
echo '  <passkey value="1c49a968f7733ae91c49a968f7733ae9"/>       <!-- 32 hexadecimal characters if provided -->' >> config.xml;
echo '  <account-token value="yEmH5yElTm8hYTm8L08PtL08E96ugE-HhIZ-3hIVWwQ" />' >> config.xml
echo '  <cpus value="@@MAX@@" />' >> config.xml
echo '</config>' >> config.xml;
echo "================Modify config================";
SEARCH="@@MAX@@"
REPLACE=$(nproc)
sed -i "s/$SEARCH/$REPLACE/" config.xml
echo "========(BETA) install start folding dependency===========";
sudo apt-get -y install python3-pip
sudo pip install --no-input lufah
echo "=========Start working================";
./fah-client > /dev/null 2>&1 &
echo "========(BETA) use utility to start folding===========";
lufah . fold
echo "=========Create hard link of log file===============";
#Give the fah client a few seconds to create the log file
sleep 10;
sudo rm -rf /mnt/log.txt --verbose;
sudo ln /mnt/batch/tasks/startup/wd/runner/fclient/log.txt /mnt/;
sudo chmod +r /mnt/log.txt --verbose;
echo "=========Finished================";
