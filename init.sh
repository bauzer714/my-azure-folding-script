#!/usr/bin/env bash

echo "=========Attempting to update and upgrade================";
sudo apt-get -y update;
sudo apt-get -y upgrade;
echo "=========Installing dependencies================";
sudo apt -y install bzip2;
echo "=========Moving to working directory================";
cd /home/phpsudo;
pwd;
echo "=========Download fahclient================";
wget https://download.foldingathome.org/releases/public/release/fahclient/debian-stable-64bit/v7.6/latest.tar.bz2;
echo "=========Prepare subdirectories================";
rm -f latest*;
rm -rf fclient;
mkdir fclient;
tar jxf lat*;
cp -r fah*/* fclient;
cd fclient;

echo "=========Build config================";

echo '<config>' > config.xml;
echo '  <user value="bauzer714"/> <!-- Enter your user name here -->' >> config.xml;
echo '  <team value="47180"/>         <!-- Your team number -->' >> config.xml;
echo '  <passkey value="1c49a968f7733ae91c49a968f7733ae9"/>       <!-- 32 hexadecimal characters if provided -->' >> config.xml;
echo '  <power value="full"/>' >> config.xml;
echo '  <gpu value="true"/>         <!-- If true, attempt to autoconfigure GPUs -->' >> config.xml;
echo '  <fold-anon value="false"/>' >> config.xml;
echo '  <checkpoint value="5" />' >> config.xml;
echo '  <respawn value="true" />' >> config.xml;
echo '  <fork value="true" />' >> config.xml;
echo '</config>' >> config.xml;

echo "=========Start working================";

./FAHClient > /dev/null 2>&1 &

echo "=========Finished================";
