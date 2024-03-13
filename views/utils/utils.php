<?php
function formatDate($date)
{
    // Si no está definida la fecha de entrega se indica
    if ($date === '0000-00-00') return 'No definida';
    // Convertir la fecha a objeto DateTime
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);

    // Establecer la configuración regional a español
    setlocale(LC_TIME, 'es_ES.UTF-8');

    return strftime('%d-%m-%Y', $dateObj->getTimestamp());
    // 
}
