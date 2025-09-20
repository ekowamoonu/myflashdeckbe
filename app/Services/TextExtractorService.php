<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;

class TextExtractorService
{
    public function extractText(UploadedFile $uploadedFile): string
    {
        $fileExtension = $uploadedFile->getClientOriginalExtension();
        $fileRealPath = $uploadedFile->getRealPath();
        $extractedText = "";

        switch ($fileExtension) {
            case 'pdf':
                $parser = new Parser();
                $pdf = $parser->parseFile($fileRealPath);
                $extractedText = $pdf->getText();
                break;
            default:
                break;
        }

        return $extractedText;
    }
}
