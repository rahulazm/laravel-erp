<?php

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'USD') {
        return number_format($amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}