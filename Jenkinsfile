pipeline {
     agent {
        label 'docker'  // Usa un nodo con Docker instalado
    }
    stages {

        stage('Verificar Docker') {
            steps {
                sh 'docker --version || echo "Docker no disponible"'
                sh 'docker compose version || echo "Docker Compose no disponible"'
            }
        }

        stage('Verificar archivos') {
            steps {
                sh 'ls -la'
            }
        }

        stage('Clonar repositorio') {
            steps {
                git branch: 'main', url: 'https://github.com/segundo-semestre-2024/grupo_1.git'
            }
        }

        stage('Limpiar contenedor viejo') {
            steps {
                script {
                    sh '''
                    if [ $(docker ps -a -q -f name=jenkins-services) ]; then
                        docker rm -f jenkins-services
                    fi
                    '''
                }
            }
        }

        stage('Levantar contenedores') {
            steps {
                sh 'docker compose up -d --build'
            }
        }

        stage('Esperar servicios') {
            steps {
                sh 'sleep 10'
            }
        }

        stage('Migraciones y seed') {
            steps {
                sh 'docker exec gateway_service php artisan migrate:fresh --seed'
            }
        }

        stage('Pruebas unitarias') {
            steps {
                sh 'docker exec gateway_service php artisan test'
            }
        }

        stage('Apagar contenedores') {
            steps {
                sh 'docker compose down'
            }
        }
    }
}
