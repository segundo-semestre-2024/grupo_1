pipeline {
    agent any  // Usa cualquier agente disponible (asegúrate de que tenga Docker instalado)
    stages {
        stage('Verificar Docker') {
            steps {
                script {
                    try {
                        sh 'docker --version'
                        sh 'docker-compose --version'
                    } catch (Exception e) {
                        error("Docker no está instalado en este agente.")
                    }
                }
            }
        }

        stage('Clonar repositorio') {
            steps {
                git branch: 'main', url: 'https://github.com/segundo-semestre-2024/grupo_1.git'
            }
        }

        stage('Levantar contenedores') {
            steps {
                sh 'docker compose up -d --build'
            }
        }

        stage('Ejecutar migraciones y pruebas') {
            steps {
                sh 'docker compose exec gateway_service php artisan migrate:fresh --seed'
                sh 'docker compose exec gateway_service php artisan test'
            }
        }

        stage('Apagar contenedores') {
            steps {
                sh 'docker compose down'
            }
        }
    }
}