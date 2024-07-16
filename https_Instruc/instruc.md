https://dev.to/iahtisham/how-to-enable-https-on-xampp-server-mb1

1. Install XAMPP

2. Navigate to C:\xampp\apache\conf\extra
   Open httpd-vhosts-conf file in Notepad
   Add following code
# Virtual Hosts
<VirtualHost *:443>
    DocumentRoot "C:/xampp/htdocs/"
    ServerName localhost
    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt" 
    SSLCertificateKeyFile "conf/ssl.key/server.key"
    <Directory "C:/xampp/htdocs/">
        Options All
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

3. Navigate to the directory C:\xampp\apache
   Open makecert.bat
   put localhost in all

4. Navigate to C:\xampp\apache\conf
    Open httpd.config
    Add the line
# Virtual hosts
Include conf/extra/httpd-vhosts.conf

5. Configure Chrome
chrome://flags/#allow-insecure-localhost
Allow/Enable