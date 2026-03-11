#!/bin/bash

echo "--- Instalando Docker y Docker Compose ---"
sudo apt-get update
sudo apt-get install -y docker.io docker-compose

# Añadir al usuario vagrant al grupo docker para evitar usar sudo después
sudo usermod -aG docker vagrant

echo "--- Levantando los contenedores desde /vagrant ---"
cd /vagrant
sudo docker-compose up -d

echo "--- Proceso finalizado. Accede a http://192.168.1.5:8080 ---"