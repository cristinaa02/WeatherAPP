<?php
require_once("model/WeatherDAO.php");

class WeatherController {
    private $apiKey= "c1b0230e2cb9f890b79276c1a818b3db";

    private function vista($contenido) {
        echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Weather App</title>
        <link rel='stylesheet' href='style.css'>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200' />
    </head>
    <body>
        <main class='main-container'>
            <form class='input-container' action='index.php?action=buscar' method='POST'>
                <input class='city' placeholder='Buscar ciudad...' type='text' name='ciudad' required>
                <button type='submit' class='search-btn'>
                    <span class='material-symbols-outlined'>search</span>
                </button>
            </form>
            $contenido
        </main>
    </body>
    </html>";
    }

    public function index() {
        $this->vista("<p>Introduce el nombre de una ciudad</p>");
    }


    //pagina normal, de buscar ciudad y mostrar tiempo
    public function buscar() {
        if (isset($_POST['ciudad'])) {
            $ciudadInput = $_POST['ciudad'];
        } else {
            $ciudadInput = "";
        }

        $url="http://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($ciudadInput) . "&limit=1&appid=" . $this->apiKey;
        $respuesta = @file_get_contents($url);
        $datos = json_decode($respuesta, true);

        if ($datos != null && count($datos) > 0) {
            $primerResultado=$datos[0];
            $nombre=$primerResultado['name'];
            $lat=$primerResultado['lat'];
            $lon=$primerResultado['lon'];

            $html = "
                <div class='weather-info' style='width: 100%; text-align: center; margin-top: 20px;'>
                    <h3 style='margin-bottom: 25px; color: white;'>📍 Ciudad: $nombre</h3>
                    <div style='display: flex; flex-direction: column; gap: 15px;'>
                        <a href='index.php?action=actual&lat=$lat&lon=$lon&name=" . urlencode($nombre) . "' class='btn-lista' style='display: block; background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px; text-decoration: none; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);'>Ver Clima Hoy</a>
                        <a href='index.php?action=porHoras&lat=$lat&lon=$lon' class='btn-lista' style='display: block; background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px; text-decoration: none; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);'>Previsión por Horas</a>
                        <a href='index.php?action=semanal&lat=$lat&lon=$lon' class='btn-lista' style='display: block; background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px; text-decoration: none; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);'>Previsión Semanal</a>
                    </div>
                </div>";
            $html .= "
                <div style='margin-top: 25px; text-align: center; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 15px;'>
                    <a href='index.php?action=historial' style='text-decoration: none; font-size: 0.9rem; color: white; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);'>Ver Historial</a>
                </div>";

        $this->vista($html);
        } else {
            $this->vista("<p style='color:red;'>❌ No se ha encontrado la ciudad: " . $ciudadInput . "</p>");
        }
    }

public function actual() {
    $lat= $_GET['lat'];
    $lon= $_GET['lon'];
    $name= $_GET['name'];

    $url = "https://api.openweathermap.org/data/2.5/weather?lat=" . $lat . "&lon=" . $lon . "&units=metric&lang=es&appid=" . $this->apiKey;
    $respuesta = file_get_contents($url);
    $data = json_decode($respuesta, true);

    $temperatura=$data['main']['temp'];
    $descripcion=$data['weather'][0]['description'];
    $estado = strtolower($data['weather'][0]['main']);
    $iconoCustom = "assets/weather/$estado.svg";

    $weatherDAO=new WeatherDAO();
    $weatherDAO->insertSearch($name, $lat, $lon, $temperatura, $descripcion, $estado);

    $html = "<section class='weather-info'>";
    $html .= "  <div class='location-date-container'>";
    $html .= "      <div class='location'><span class='material-symbols-outlined'>location_on</span><h4 class='country-txt'>$name</h4></div>";
    $html .= "      <h5 class='current-date-txt regular-txt'>" . date('d M') . "</h5>";
    $html .= "  </div>";
    $html .= "  <div class='weather-sum-container'>";
    $html .= "      <img src='$iconoCustom' class='weather-sum-img'>";
    $html .= "      <div class='weather-sum-info'>";
    $html .= "          <h1 class='temp-txt'>" . round($temperatura) . "°C</h1>";
    $html .= "          <h3 class='condition-txt regular-txt'>" . ucfirst($descripcion) . "</h3>";
    $html .= "      </div>";
    $html .= "  </div>";
    $html .= "  <br><a href='index.php' style='color:white; font-size:12px;'>Volver</a>";
    $html .= "</section>";
    
    $this->vista($html);

    }

    //pagina de prevision por horas
    public function porHoras() {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    $url = "https://api.openweathermap.org/data/2.5/forecast?lat=" . $lat . "&lon=" . $lon . "&units=metric&lang=es&appid=" . $this->apiKey;
    $datos = json_decode(file_get_contents($url), true);
    $lista = array_slice($datos['list'], 0, 8); // Cogemos las próximas 24h (8 bloques de 3h)

    // 1. Preparamos los datos para la gráfica
    $labels = [];
    $temps = [];
    foreach ($lista as $bloque) {
        $labels[] = substr($bloque['dt_txt'], 11, 5); // Solo la hora (HH:mm)
        $temps[] = $bloque['main']['temp'];
    }

    $jsonLabels = json_encode($labels);
    $jsonTemps = json_encode($temps);

    // 2. Montamos el HTML
    $html = "<div class='card'>";
    $html .= "<h2>Próximas 24 horas</h2>";
    
    // Contenedor de la gráfica
    $html .= "<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>";
    $html .= "<div style='width: 100%; max-width: 600px; margin: auto;'>";
    $html .= "<canvas id='graficoHoras'></canvas>";
    $html .= "</div>";

    // Script de la gráfica
    $html .= "<script>
        const ctx = document.getElementById('graficoHoras').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: $jsonLabels,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: $jsonTemps,
                    borderColor: '#000000',
                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#000000'
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: false, 
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { 
                            color: '#000000', // Números del eje Y en negro
                            font: { size: 14, weight: 'bold' },
                            callback: function(value) { return value + '°'; }
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            color: '#000000', // Números del eje X en negro
                            font: { size: 12, weight: 'bold' } 
                        }
                    }
                }
            }
        });
    </script>";

    $html .= "<br><a href='index.php' class='btn'>Volver</a>";
    $html .= "</div>";

    $this->vista($html);

    }

    //prevision de los siguientes 5 dias
    public function semanal() {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $url = "https://api.openweathermap.org/data/2.5/forecast?lat=" . $lat . "&lon=" . $lon . "&units=metric&lang=es&appid=" . $this->apiKey;
    $datos = json_decode(@file_get_contents($url), true);

    // CONTENEDOR TRANSPARENTE (Cristal)
    $html = "<div style='background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 15px; margin-top: 10px; width: 100%; box-sizing: border-box; border: 1px solid rgba(255, 255, 255, 0.3);'>";
    $html .= "<h3 style='color: #000; text-align: center; margin: 0 0 10px 0; font-size: 1.1rem; font-weight: 800;'>Pronóstico 5 días</h3>";
    
    $html .= "<table style='width: 100%; border-collapse: collapse; table-layout: fixed;'>";
    $html .= "<thead><tr style='border-bottom: 2px solid rgba(0,0,0,0.2);'>";
    $html .= "<th style='color:#000; padding:4px; text-align:left; font-size:0.8rem; width:25%;'>Fecha</th>";
    $html .= "<th style='color:#000; padding:4px; text-align:center; font-size:0.8rem; width:25%;'>Temp</th>";
    $html .= "<th style='color:#000; padding:4px; text-align:right; font-size:0.8rem; width:50%;'>Clima</th></tr></thead>";
    $html .= "<tbody>";

    $diaAnterior = "";
    foreach ($datos['list'] as $bloque) {
        $soloFecha = substr($bloque['dt_txt'], 0, 10);
        if ($soloFecha != $diaAnterior) {
            $temp = round($bloque['main']['temp']);
            $desc = $bloque['weather'][0]['description'];
            $icono = $bloque['weather'][0]['icon'];
            $fechaFormateada = date("d/m", strtotime($soloFecha));

            $html .= "<tr style='border-bottom: 1px solid rgba(0,0,0,0.05);'>";
            $html .= "<td style='color:#000; padding:10px 0; font-size:0.85rem; font-weight:bold;'>$fechaFormateada</td>";
            $html .= "<td style='color:#000; padding:10px 0; text-align:center; font-weight:900; font-size:1rem;'>$temp</td>";
            $html .= "<td style='color:#000; padding:10px 0; text-align:right; font-size:0.75rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'>";
            $html .= "<img src='http://openweathermap.org/img/wn/$icono.png' style='width:22px; vertical-align:middle; filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.2));'> " . ucfirst($desc);
            $html .= "</td></tr>";
            $diaAnterior = $soloFecha;
        }
    }
    $html .= "</tbody></table></div>";
    
    $html .= "<div style='margin-top: 15px; text-align: center;'>";
    $html .= "<a href='index.php' style='color: white; font-weight: bold; text-decoration: none; font-size: 0.9rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);'>⬅ Volver</a>";
    $html .= "</div>";

    $this->vista($html);
}


    //historial
    public function historial() {
    $weatherDAO = new WeatherDAO();
    $registros = $weatherDAO->getAllSearches(); 

    // CONTENEDOR CRISTAL (Igual que el semanal)
    $html = "<div style='background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 15px; margin-top: 10px; width: 100%; box-sizing: border-box; border: 1px solid rgba(255, 255, 255, 0.3);'>";
    $html .= "<h3 style='color: #000; text-align: center; margin: 0 0 10px 0; font-size: 1.1rem; font-weight: 800;'>Historial de Búsquedas</h3>";
    
    $html .= "<table style='width: 100%; border-collapse: collapse; table-layout: fixed;'>";
    $html .= "<thead><tr style='border-bottom: 2px solid rgba(0,0,0,0.2);'>";
    $html .= "<th style='color:#000; padding:5px; text-align:left; font-size:0.8rem; width:40%;'>Ciudad</th>";
    $html .= "<th style='color:#000; padding:5px; text-align:center; font-size:0.8rem; width:20%;'>Temp</th>";
    $html .= "<th style='color:#000; padding:5px; text-align:right; font-size:0.8rem; width:40%;'>Fecha</th></tr></thead>";
    $html .= "<tbody>";

    if ($registros) {
        foreach ($registros as $r) {
            // Formateamos la fecha para que no ocupe tanto espacio
            $fechaCorta = date("d/m H:i", strtotime($r['fecha_consulta']));
            $temp = round($r['temperatura']);

            $html .= "<tr style='border-bottom: 1px solid rgba(0,0,0,0.05);'>";
            $html .= "<td style='color:#000; padding:10px 0; font-size:0.85rem; font-weight:bold; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;'>".$r['ciudad']."</td>";
            $html .= "<td style='color:#000; padding:10px 0; text-align:center; font-weight:900; font-size:1rem;'>$temp</td>";
            $html .= "<td style='color:#000; padding:10px 0; text-align:right; font-size:0.75rem; font-weight:600;'>$fechaCorta</td>";
            $html .= "</tr>";
        }
    } else {
        $html .= "<tr><td colspan='3' style='color:#000; text-align:center; padding:20px;'>No hay búsquedas recientes</td></tr>";
    }

    $html .= "</tbody></table></div>";
    
    // Botón volver
    $html .= "<div style='margin-top: 15px; text-align: center;'>";
    $html .= "<a href='index.php' style='color: white; font-weight: bold; text-decoration: none; font-size: 0.9rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);'>⬅ Volver</a>";
    $html .= "</div>";

    $this->vista($html);

    }

}