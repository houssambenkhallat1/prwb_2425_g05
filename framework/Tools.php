<?php

require_once 'View.php';

class Tools {

    //nettoie le string donné
    public static function sanitize(string $var): string {
        return trim(htmlspecialchars($var, ENT_QUOTES, "UTF-8"));
    }

    //dirige vers la page d'erreur
    public static function abort(string $err): void {
        http_response_code(500);
        (new View("error"))->show(array("error" => $err));
        die;
    }

    public static function start($start): string
    {
        try {
            $now = new DateTime();
            $interval = $start->diff($now);

            // Tableau des unités de temps avec leur format
            $timeUnits = [
                'y' => ['year', 'years'],
                'm' => ['month', 'months'],
                'd' => ['day', 'days'],
                'h' => ['heure', 'heures'],
                'i' => ['minute', 'minutes'],
                's' => ['seconde', 'secondes']
            ];

            // Parcourir les unités de temps pour trouver la première non nulle
            foreach ($timeUnits as $unit => $labels) {
                $value = $interval->$unit;
                if ($value > 0) {
                    $label = $value === 1 ? $labels[0] : $labels[1];
                    return sprintf('%d %s', $value, $label).' ago';
                }
            }

            return "A l'instant";
        } catch (Exception $e) {
            return "durée indéterminée";
        }
    }
//    public static function abort(string $err, ?string $details = null): void {
//        http_response_code(500); // Ou un autre code pertinent si connu
//        $data = ["error" => $err];
//        if (Configuration::is_dev() && $details) {
//            $data["exception_details"] = $details;
//        }
//        (new View("error"))->show($data);
//        die;
//    }
}
