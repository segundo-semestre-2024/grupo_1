pipeline {
    agent any

    environment {
        
        LARAVEL_ENV = 'X_API_KEY=1234\nAPI_KEY=1234\nMICROSERVICIO_FLASK=http://sistema:5000'
        SISTEMA_ENV = 'NOTIFICACIONES_URL=http://notificaciones:8001/api/send-sms\nAPI_KEY=1234'
    }

    stages {
        stage('Limpiar workspace') {
            steps {
                deleteDir()
            }
        }

        stage('Clonar repositorio') {
            steps {
               git url: 'https://github.com/segundo-semestre-2024/grupo_1.git', branch: 'main'
            }
        }

        stage('Crear .env de gateway') {
            steps {
                writeFile file: 'gateway/.env', text: "${env.LARAVEL_ENV}"
            }
        }

        stage('Crear .env de sistema') {
            steps {
                writeFile file: 'sistema/.env', text: "${env.SISTEMA_ENV}"
            }
        }

        stage('Construir contenedores') {
            steps {
                sh 'docker compose build'
            }
        }
        
        stage('Limpiar contenedores anteriores') {
            steps {
                sh 'docker rm -f mysql_service || true'
            }
        }


        stage('Levantar contenedores') {
            steps {
                sh 'docker compose up -d'
            }
        }

        stage('Instalar dependencias') {
            steps {
                sh '''
                    docker compose exec gateway composer install
                    docker compose exec sistema pip install -r sistema/requirements.txt
                '''
            }
        }

        stage('Esperar MySQL') {
            steps {
                sh '''
                    docker compose exec mysql_serviceneutro bash -c '
                        for i in {1..10}; do
                            if mysqladmin ping -h localhost --silent; then
                                exit 0
                            fi
                            sleep 5
                        done
                        exit 1
                    '
                '''
            }
        }

        stage('Migrar base de datos') {
            steps {
                sh '''
                    docker compose exec gateway php artisan key:generate
                    docker compose exec gateway php artisan migrate --force
                '''
            }
        }

        stage('Ejecutar pruebas de Laravel') {
            steps {
                sh 'docker compose exec gateway php artisan test'
            }
        }
    }

    post {
        always {
            sh 'docker compose down'
        }
    }
}
