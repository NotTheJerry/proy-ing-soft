<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PDFService
{
    /**
     * Generate a PDF from a view
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paperSize
     * @param string $orientation
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePDF($view, $data = [], $filename = 'document.pdf', $paperSize = 'a4', $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper($paperSize, $orientation)
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'debugCss' => false,
                'debugLayout' => false,
                'chroot' => public_path(),
            ]);
            
        return $pdf;
    }

    /**
     * Stream a PDF file to the browser
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paperSize
     * @param string $orientation
     * @return \Illuminate\Http\Response
     */
    public function streamPDF($view, $data = [], $filename = 'document.pdf', $paperSize = 'a4', $orientation = 'portrait')
    {
        $pdf = $this->generatePDF($view, $data, $filename, $paperSize, $orientation);
        return $pdf->stream($filename);
    }

    /**
     * Download a PDF file
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paperSize
     * @param string $orientation
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($view, $data = [], $filename = 'document.pdf', $paperSize = 'a4', $orientation = 'portrait')
    {
        $pdf = $this->generatePDF($view, $data, $filename, $paperSize, $orientation);
        return $pdf->download($filename);
    }
}
