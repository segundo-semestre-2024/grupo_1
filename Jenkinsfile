pipeline {
    agent any

    environment {
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

        stage('Levantar contenedores') {
            steps {
                sh 'docker compose up -d'
            }
        }

        stage('Copiar .env al contenedor gateway') {
            steps {
                withCredentials([file(credentialsId: 'gateway-dotenv', variable: 'LARAVEL_DOTENV_FILE')]) {
                    sh '''
                        # Copiamos el archivo secreto al contenedor gateway
                        docker cp "$LARAVEL_DOTENV_FILE" $(docker compose ps -q gateway):/var/www/html/.env
                    '''
                }
            }
        }

        stage('Ver logs del servicio sistema') {
            steps {
                sh 'docker compose logs --tail=100 sistema'
            }
        }

        stage('Verificar contenedores') {
            steps {
                echo '🔍 Verificando contenedores...'
                sh 'docker compose ps'
            }
        }

        stage('Instalar dependencias') {
            steps {
                sh 'docker compose exec gateway composer install'
            }
        }

        stage('Esperar MySQL') {
            steps {
                sh '''
                    docker compose exec mysql bash -c '
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
                    docker compose exec gateway php artisan db:seed --force
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