pipeline {
    agent any
    stages {
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


          stage('limpiar contendor viejo ') {
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

        stage('correr el  container') {
            steps {
                sh 'docker run -d --name jenkins-services -p 8080:8080 jenkins/jenkins:lts'
            }
        }
        stage('Esperar servicios') {
            steps {
                sh 'sleep 10' // Puedes usar "wait-for-it" o healthcheck si quieres ser pro
            }
        }
        stage('Migraciones y seed') {
            steps {
                sh 'docker exec gateway_service php artisan migrate:fresh --seed'
    }
}
        stage('Pruebas unitarias') {
            steps {
                sh 'docker exec gateway_service bash -c php artisan test'
    }
}
        stage('Apagar contenedores') {
            steps {
                sh 'docker-compose down'
            }
        }
    }
}
