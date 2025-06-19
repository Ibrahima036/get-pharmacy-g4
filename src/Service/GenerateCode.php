<?php

namespace App\Service;

class GenerateCode
{
    function generateCodeProduct(): string
    {
        $prefix = 'PRD';
        $dateTime = date('Ymd-His');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return "{$prefix}-{$dateTime}-{$random}";
    }
}