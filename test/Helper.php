<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
class Helper{

    public static $hal;
    public static function addQr($pathFile,$x=0,$y=0,$hal=1)
    {
        require_once('fpdf/fpdf.php');
        require_once('FPDI/src/autoload.php');

        $pdf = new \setasign\Fpdi\Fpdi;
    
        $pageCount = $pdf->setSourceFile($pathFile);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pageId = $pdf->importPage($pageNo);
            $specs = $pdf->getTemplateSize($pageId);
            
            $pdf->addPage($specs['height'] > $specs['width'] ? 'P' : 'L',[$specs['width'], $specs['height']]);
            $pdf->useImportedPage($pageId);
            if ($pageNo == $hal){
                
                self::setStamp2($pdf,$x,$y);
            }
        }
       
        self::$hal = $hal;
        header("Content-type:application/pdf");
        $pdf->Output('');
       
      
    }

    public static function setStamp2($pdf,$x,$y)
    {
        $arrContextOptions=array(
            "ssl"=>array(
                "allow_self_signed"=>true,
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  
        $url = 'testqrcode';
        $file_content = file_get_contents('https://esurat.dephub.go.id/site/qr?msg='.$url, false, stream_context_create($arrContextOptions));
        $pegawai['nama'] = 'Nama Contoh Saja';

        $pdf->SetFont('Arial','',7);
        $pdf->SetXY($x,$y);
        $x_qr = intval($pdf->GetX())+2;
        $y_qr = intval($pdf->GetY())+2;
        $pdf->MemImage($file_content,$x_qr,$y_qr,11,11,'PNG');
        
        
        $pdf->MultiCell(88, 20, '' ,0);
        $pdf->SetXY($x_qr-1,$y_qr+11);
        $pdf->MultiCell(60, 3, 'Ditandatangani secara elektronik' ,0);

        $pdf->SetFont('Arial','',8);
        $pdf->SetXY($x_qr-1,$y_qr+14);
        $pdf->MultiCell(60, 3, strtoupper($pegawai['nama']) ,0);
     
      
    }
}

Helper::addQr(__DIR__.'/compressed.tracemonkey-pldi-09.pdf',$_GET['x'],$_GET['y'],1);
    