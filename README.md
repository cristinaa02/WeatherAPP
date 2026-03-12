# WeatherAPP

Una web para consultar el pronóstico del tiempo, desarrollada con **PHP 8.2** y desplegada en **AWS** utilizando **Docker**.

##  índice
1. [Web](#web)
2. [Descripción](#descripción)
3. [Estructura del proyecto](#estructura)
4. [Tecnologías utilizadas](#tecnologías-utilizadas)
5. [Despliegue en AWS](#despliegue-en-aws)

---

## Web
Puedes probar la aplicación funcionando en AWS aquí: [http://52.206.56.65](http://52.206.56.65)

---

## Descripción
Esta aplicación permite obtener datos meteorológicos de una ciudad. Se muestra el clima actual y un pronóstico detallado para los próximos 5 días, organizado en tablas.

---

## Estructura

El proyecto está organizado de la siguiente manera:

* **`src/`**: Carpeta principal del código fuente.
    * **`assets/`**: Contiene imágenes de fondo (`bg.jpg`), iconos de clima y otros recursos.
    * **`controller/`**: Gestión de la lógica de la aplicación mediante `WeatherController.php`.
    * **`model/`**: Con `WeatherDAO.php` , gestiona insercciones y consultas a la base de datos.
    * **`db/`**: Configuración de conexión en `db.php`.
    * **`index.php` & `style.css`**: Interfaz de usuario y diseño.
* **`docker_install.sh`**: Script de automatización para desplegar todo en un solo paso.
* **`docker-compose.yml` & `Dockerfile`**: Configuración de la infraestructura.
* **`database.sql`**: Esquema de base de datos para el soporte de historial.

---

## Tecnologías utilizadas
* **Lenguaje:** PHP 8.2
* **Servidor Web:** Apache
* **API:** OpenWeatherMap API
* **Contenedores:** Docker & Docker Compose
* **Plataforma:** AWS

---

## Despliegue en AWS

Para desplegar esta aplicación en una instancia EC2 de AWS:

1. **Conectarse a la instancia AWS** vía SSH.
2. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/cristinaa02/WeatherAPP.git
3. **Entrar en la carpeta, dar permisos al .sh (chmod) y ejecutar el script**.
4. **Visualizar con la IP de AWS la Web**.
