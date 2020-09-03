<?php

namespace AG\PollChart\Output;


// class
class OutputData
{
    public $data;

    public function __construct() {
        $this->data = [];
    }
    public function __destruct() {}

    public function injectDataToHtml($data): string
    {
        $html = '<div style="background: #fefefe; padding: 5px;">';
        $html .= '<h2 style="margin-bottom: 0;">' . $data['name'] . '</h2>';
        $html .= '<ul class="no-bullets">';
        $html .= '<li style="color: #999; margin-top: 0 !important;">' . $data['time'] . '</li>';
        $html .= '<li style="display: inline-block; vertical-align: middle; font-weight: 500;">' . $data['weatherDescription'] . $data['weatherIcon'] . '</li>';

        $html .= '<li>' . __('hőmérséklet: ') . round($data['temperature']) . ' °C</li>';
        $html .= '<li>' . __('páratartalom: ') . $data['humidity'] . '%</li>';
        $html .= '<li>' . __('szél: ') . round($data['windSpeed']) . ' km/h ' . $data['windDirection'] . '</li>';
        $html .= '<li><a href="' . $data['mapUrl'] . '" target="_blank">' . __('Időjárástérkép') . '</a></li>';
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    public function convertFormat(
        array $data,
        string $mapUrl = 'https://openweathermap.org/weathermap'
    ): array {
        // store data as associative array
        $weatherData = [];
        
        // Get city name
        $weatherData['name'] = ($data['name']) ? ($data['name']) : ('');

        // correct UTC time with timezone
        $timestamp = intval($data['dt'], 10) + intval($data['timezone'], 10);

        // get time hour:minutes
        // get date and local time
        $weatherData['time'] = date('H:m', $timestamp);

        // get latitude and longitude
        $weatherData['latitude'] = ($data['coord']['lat']) ?
            ($data['coord']['lat']) : ('');

        $weatherData['longitude'] = ($data['coord']['lon']) ?
            ($data['coord']['lon']) : ('');


        // create map link
        if ($weatherData['latitude'] && $weatherData['longitude']) {
            $weatherData['mapUrl'] = $mapUrl .
                "?zoom=12&lat={$weatherData['latitude']}" .
                "&lon={$weatherData['longitude']}";
        } else {
            $weatherData['mapUrl'] = '';
        }

        // get temperature data
        $weatherData['temperature'] = ($data['main']['temp']) ?
            (floatval($data['main']['temp'])) : (null);

        // get humidity in percent
        $weatherData['humidity'] = ($data['main']['humidity']) ?
            ($data['main']['humidity']) : ('');


        // get precipitation amount for the previous 1 hour
        $weatherData['rain1h'] = (isset($data['rain']['1h'])) ?
            (intval($data['rain']['1h'])) : (null);

        // get windspeed
        $weatherData['windSpeed'] = ($data['wind']['speed']) ?
            (floatval($data['wind']['speed'])) : (null);

        // get wind direction
        if ($data['wind']['deg']) {
            $windDirection = intval($data['wind']['deg'], 10);
            // get wind direction in cardinal directions 
            $weatherData['windDirection'] = $this->getWindDirection($windDirection);
        } else {
            $weatherData['windDirection'] = '';
        }

        // get short weather description
        if ($data['weather'][0]['description']) {
            $weatherData['weatherDescription'] = $data['weather'][0]['description'];
        } else {
            $weatherData['weatherDescription'] = '';
        }

        // get weather state id
        if ($data['weather'][0]['icon']) {
            $weatherIcon = $data['weather'][0]['icon'];

            if ($weatherData['weatherDescription']) {
                // get weather icon img sources from open weather
                $weatherData['weatherIcon'] = $this->getImage($weatherIcon, $weatherData['weatherDescription']);
            }
        } else {
            $weatherData['weatherIcon'] = '';
        }

        return $weatherData;
        // used for for method chaining
        // return $this;
    }


    // generate HTML code for img
    private function getImage(string $weatherIcon, string $weatherDescription, string $imageUrl = 'http://openweathermap.org/img/wn/'): string
    {
        $weatherImg = '<img class="ag-weather-now icon" src="' . esc_url($imageUrl . $weatherIcon) .
            '@2x.png" alt="' . $weatherDescription . '" />';

        return $weatherImg;
    }


    // get wind directions as the wind rose says
    private function getWindDirection(int $windDirection): string
    {
        $windRose = '';
        if ($windDirection >= 349 && $windDirection < 11) {
            $windRose = __('É');
        } else if ($windDirection >= 11 && $windDirection < 34) {
            $windRose = __('ÉÉK');
        } else if ($windDirection >= 34 && $windDirection < 56) {
            $windRose = __('ÉK');
        } else if ($windDirection >= 56 && $windDirection < 79) {
            $windRose = __('KÉK');
        } else if ($windDirection >= 79 && $windDirection < 101) {
            $windRose = __('K');
        } else if ($windDirection >= 101 && $windDirection < 124) {
            $windRose = __('KDK');
        } else if ($windDirection >= 124 && $windDirection < 146) {
            $windRose = __('DK');
        } else if ($windDirection >= 146 && $windDirection < 169) {
            $windRose = __('DDK');
        } else if ($windDirection >= 169 && $windDirection < 191) {
            $windRose = __('D');
        } else if ($windDirection >= 191 && $windDirection < 214) {
            $windRose = __('DDNY');
        } else if ($windDirection >= 214 && $windDirection < 236) {
            $windRose = __('DNY');
        } else if ($windDirection >= 236 && $windDirection < 259) {
            $windRose = __('NYDNY');
        } else if ($windDirection >= 259 && $windDirection < 281) {
            $windRose = __('NY');
        } else if ($windDirection >= 281 && $windDirection < 304) {
            $windRose = __('NYÉNY');
        } else if ($windDirection >= 304 && $windDirection < 326) {
            $windRose = __('ÉNY');
        } else if ($windDirection >= 326 && $windDirection < 349) {
            $windRose = __('ÉÉNY');
        }

        return $windRose;
    }
}
