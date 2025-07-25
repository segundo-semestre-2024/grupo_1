-- Otorga todos los permisos al usuario 'user' desde cualquier host
GRANT ALL PRIVILEGES ON *.* TO 'user'@'%' IDENTIFIED BY 'userpassword';

-- Asegura que los cambios surtan efecto
FLUSH PRIVILEGES;
