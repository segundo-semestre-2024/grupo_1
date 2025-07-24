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
        stage('Levantar contenedores') {
            steps {
                sh 'docker compose up -d --build'
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
