#!/bin/bash

echo "--- Instalando herramientas ---"
sudo apt-get update
sudo apt-get install -y docker.io docker-compose

echo "--- Bajando código ---"
# git clone https://github.com/cristinaa02/WeatherAPP.git

echo "--- Desplegando ---"
cd WeatherAPP
sudo docker-compose up -d --build

echo "--- Proceso finalizado ---"