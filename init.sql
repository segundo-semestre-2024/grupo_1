CREATE USER 'user'@'%' IDENTIFIED BY 'userpassword';
GRANT ALL PRIVILEGES ON proyecto.* TO 'user'@'%';
FLUSH PRIVILEGES;
