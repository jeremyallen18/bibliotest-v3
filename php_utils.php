<?php

if (!function_exists('generar_extracto_inteligente')) {
    /**
     * Generates an intelligent excerpt from text, trying not to cut words.
     *
     * @param string $texto The input text.
     * @param int $longitud The desired maximum length of the excerpt.
     * @param string $sufijo The suffix to add if the text is truncated.
     * @return string The generated excerpt.
     */
    function generar_extracto_inteligente($texto, $longitud = 150, $sufijo = '...') {
        $texto_limpio = strip_tags($texto); // Remove HTML tags
        if (mb_strlen($texto_limpio) <= $longitud) {
            return htmlspecialchars($texto_limpio);
        }
        $ultimo_espacio = mb_strrpos(mb_substr($texto_limpio, 0, $longitud), ' ');
        if ($ultimo_espacio !== false) {
            return htmlspecialchars(mb_substr($texto_limpio, 0, $ultimo_espacio)) . $sufijo;
        } else {
            // If no space is found, just cut at the length (less ideal)
            return htmlspecialchars(mb_substr($texto_limpio, 0, $longitud)) . $sufijo;
        }
    }
}

if (!function_exists('formatear_fecha_relativa')) {
    /**
     * Formats a MySQL datetime stamp into a user-friendly relative time.
     *
     * @param string $fecha_mysql The MySQL datetime string (e.g., 'YYYY-MM-DD HH:MM:SS').
     * @return string The formatted relative time string.
     */
    function formatear_fecha_relativa($fecha_mysql) {
        if (empty($fecha_mysql)) {
            return '';
        }
        try {
            $fecha = new DateTime($fecha_mysql);
            $ahora = new DateTime();
            $intervalo = $ahora->diff($fecha);

            if ($intervalo->y >= 1) {
                return 'Hace ' . $intervalo->y . ' año' . ($intervalo->y > 1 ? 's' : '');
            } elseif ($intervalo->m >= 1) {
                return 'Hace ' . $intervalo->m . ' mes' . ($intervalo->m > 1 ? 'es' : '');
            } elseif ($intervalo->d >= 7) {
                $semanas = floor($intervalo->d / 7);
                return 'Hace ' . $semanas . ' semana' . ($semanas > 1 ? 's' : '');
            } elseif ($intervalo->d >= 1) {
                if ($intervalo->d == 1) return 'Ayer';
                return 'Hace ' . $intervalo->d . ' días';
            } elseif ($intervalo->h >= 1) {
                return 'Hace ' . $intervalo->h . ' hora' . ($intervalo->h > 1 ? 's' : '');
            } elseif ($intervalo->i >= 1) {
                return 'Hace ' . $intervalo->i . ' minuto' . ($intervalo->i > 1 ? 's' : '');
            } else {
                return 'Hace unos segundos';
            }
        } catch (Exception $e) {
            // Fallback for invalid date format
            error_log('Error en formatear_fecha_relativa: ' . $e->getMessage());
            // Attempt to format with original logic if DateTime fails
            $timestamp = strtotime($fecha_mysql);
            if ($timestamp === false) return 'Fecha inválida';
            return date('M d, Y', $timestamp);
        }
    }
}

?>
