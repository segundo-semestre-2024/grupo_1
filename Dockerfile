FROM jenkins/jenkins:lts

USER root

# Instalar dependencias generales y Docker
RUN apt-get update && apt-get install -y \
    php php-cli php-mbstring php-xml php-curl unzip \
    curl git apt-transport-https ca-certificates gnupg2 \
    software-properties-common lsb-release docker.io \
    && apt-get clean

# Instalar Docker Compose V2 como plugin
RUN mkdir -p ~/.docker/cli-plugins && \
    curl -SL https://github.com/docker/compose/releases/download/v2.24.5/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose && \
    chmod +x ~/.docker/cli-plugins/docker-compose

# Verificar Docker Compose V2
RUN docker compose version

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer --version

# Agregar Jenkins al grupo docker
RUN usermod -aG docker jenkins && \
    chown -R jenkins:jenkins /var/jenkins_home

USER jenkins

