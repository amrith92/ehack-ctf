<?php

namespace CTF\CertificateBundle\Services;

use CTF\CertificateBundle\Entity\CertifyData;
use fpdf\FPDF;
use Symfony\Component\HttpFoundation\Request;

class CertificateGenerator {
    
    /**
     *
     * @var string
     */
    private $baseurl;
    
    /**
     *
     * @var string
     */
    private $basepath;

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $helper
     */
    public function __construct(Request $request) {
        $this->baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseUrl();
        $this->baseurl = str_replace('/app_dev.php', '', $this->baseurl);
        $this->baseurl = str_replace('/app.php', '', $this->baseurl);
        $this->basepath = __DIR__. '/../../../../web';
    }
    
    /**
     * 
     * @param \fpdf\FPDF $pdf
     * @param string $text
     * @param integer $xval
     * @param integer $yval
     */
    private function addTextPdf(FPDF $pdf, $text, $xval, $yval) {
        $width = $pdf->GetStringWidth($text);
        $mid = (1024 - $width) / 2;
        $pdf->SetY($yval);
        $pdf->SetX($mid + $xval);
        $pdf->Cell(0, 0, $text);
    }
    
    /**
     * 
     * @param Resource $img
     * @param string $text
     * @param integer $xval
     * @param integer $yval
     */
    private function addTextPng($img, $text, $xval, $yval, $size = 18) {
        $textColor = \imagecolorallocate($img, 255, 255, 255);
        $font = $this->basepath . "/bundles/ctfcertificate/font/segoeuil.ttf";
        $dimensions = \imagettfbbox($size, 0, $font, $text);
        $textWidth = \abs($dimensions[4] - $dimensions[0]);
        $mid = 512 - ($textWidth / 2);
        $xval = $mid + $xval;
        \imagettftext($img, $size, 0, $xval, $yval, $textColor, $font, $text);
    }
    
    /**
     * 
     * @param \CTF\CertificateBundle\Entity\CertifyData $data
     * @return string
     */
    public function generatePdfCertificate(CertifyData $data) {
        $pdf = new FPDF('L', 'pt', array(1024, 650));
        $pdf->AddPage();
        $pdf->AddFont('SegoeFont', '', "segoeuil.php");
        $pdf->SetFont('SegoeFont', '', 20);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetAutoPageBreak(false);
        $pdf->Image($this->baseurl . "/bundles/ctfcertificate/images/certificate.png", 0, 0, 1024);
        
        // Add data to certificate
        $this->addTextPdf($pdf, $data->getFullName(), -50, 258);
        $this->addTextPdf($pdf, $data->getTeam(), 306, 258);
        $this->addTextPdf($pdf, $data->getOrganization(), -135, 292);
        $this->addTextPdf($pdf, $data->getScore(), 380, 292);
        $this->addTextPdf($pdf, $data->getRank(), -145, 326);
        $pdf->SetFont('SegoeFont', '', 16);
        $this->addTextPdf($pdf, $data->getTimestamp()->format('Y-m-d H:i:s'), -145, 354);
        $this->addTextPdf($pdf, $data->getSerial(), 258, 354);
        
        return $pdf->Output('EHACK-certificate.pdf', 'S');
    }
    
    /**
     * 
     * @param \CTF\CertificateBundle\Entity\CertifyData $data
     * @return Resource
     */
    public function generatePngCertificate(CertifyData $data) {
        $img = \imagecreatefrompng($this->baseurl . "/bundles/ctfcertificate/images/certificate.png");
        
        $this->addTextPng($img, $data->getFullName(), -50, 258);
        $this->addTextPng($img, $data->getTeam(), 306, 258);
        $this->addTextPng($img, $data->getOrganization(), -135, 292);
        $this->addTextPng($img, $data->getScore(), 380, 292);
        $this->addTextPng($img, $data->getRank(), -145, 326);
        $this->addTextPng($img, $data->getTimestamp()->format('Y-m-d H:i:s'), -145, 354, 12);
        $this->addTextPng($img, $data->getSerial(), 258, 354, 12);
        
        \ob_start();
        \imagepng($img);
        $image = \ob_get_contents();
        \ob_end_clean();
        
        return $image;
    }
}
