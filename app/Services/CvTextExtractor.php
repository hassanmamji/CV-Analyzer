<?php

namespace App\Services;

use RuntimeException;
use Smalot\PdfParser\Parser;

class CvTextExtractor
{
    public function extract(string $fullPath): string
    {
        $pdf  = (new Parser())->parseFile($fullPath);
        $text = trim(preg_replace('/\s+/', ' ', $pdf->getText()));

        if (mb_strlen($text) < 100) {
            throw new RuntimeException('CV se text nahi mila. Shayad scanned PDF hai, text wali PDF upload karo.');
        }

        // bohot lambi CV ko kaat do (API cost control)
        return mb_substr($text, 0, 15000);
    }
}