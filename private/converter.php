<?php
class Converter{

    private $file;
    private $objExcel;
    private $content;
    
    function __construct(){}
    
    function checkFile($file){
        try {

            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($file['error']) ||
                is_array($file['error'])
            ) {
                return 'Invalid parameters.';
            }

            // Check $file['error'] value.
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return 'No file sent.';
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return 'Exceeded filesize limit.';
                default:
                    return 'Unknown errors.';
            }

            // You should also check filesize here.
            if ($file['size'] > 1000000) {
                return 'Exceeded filesize limit.';
            }

            // DO NOT TRUST $file['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search(
                    $finfo->file($file['tmp_name']),
                    array(
                        'xls' => 'application/vnd.ms-excel',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ),
                    true
                )) {
                return 'Invalid file format.';
            }

            $this->file = $file['tmp_name'];

        } catch (RuntimeException $e) {

            return $e->getMessage();

        }
        return '';
    }

    private function initExcelFile(){
        $objReader = new PHPExcel_Reader_Excel2007();
        $objReader->setReadDataOnly(true);
        $this->objExcel = $objReader->load( $this->file );
    }

    private function getArray(){
        $rowIterator = $this->objExcel->getActiveSheet()->getRowIterator();

        $array_data = array();
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            if(1 == $row->getRowIndex ()) continue;
            $rowIndex = $row->getRowIndex ();
            $array_data[$rowIndex] = array();

            foreach ($cellIterator as $cell) {
                if('A' == $cell->getColumn()){
                    $array_data[$rowIndex]['vardas'] = $cell->getFormattedValue();
                } else if('B' == $cell->getColumn()){
                    $array_data[$rowIndex]['pavarde'] = $cell->getFormattedValue();
                } else if('C' == $cell->getColumn()){
                    $array_data[$rowIndex]['vardas_pavarde'] = $cell->getFormattedValue();
                } else if('D' == $cell->getColumn()){
                    $array_data[$rowIndex]['ak'] = $cell->getFormattedValue();
                } else if('E' == $cell->getColumn()){
                    $array_data[$rowIndex]['0ak'] = $cell->getFormattedValue();
                } else if('F' == $cell->getColumn()){
                    $array_data[$rowIndex]['gimimo_data'] = $cell->getFormattedValue();
                } else if('G' == $cell->getColumn()){
                    $array_data[$rowIndex]['lytis'] = $cell->getFormattedValue();
                } else if('H' == $cell->getColumn()){
                    $array_data[$rowIndex]['el_pastas'] = $cell->getFormattedValue();
                } else if('I' == $cell->getColumn()){
                    $array_data[$rowIndex]['tel'] = $cell->getFormattedValue();
                } else if('J' == $cell->getColumn()){
                    $array_data[$rowIndex]['gatve'] = $cell->getFormattedValue();
                } else if('K' == $cell->getColumn()){
                    $array_data[$rowIndex]['miestas'] = $cell->getFormattedValue();
                } else if('L' == $cell->getColumn()){
                    $array_data[$rowIndex]['katedra'] = $cell->getFormattedValue();
                } else if('M' == $cell->getColumn()){
                    $array_data[$rowIndex]['studiju_programa'] = $cell->getFormattedValue();
                } else if('N' == $cell->getColumn()){
                    $array_data[$rowIndex]['kursas'] = $cell->getFormattedValue();
                } else if('O' == $cell->getColumn()){
                    $array_data[$rowIndex]['studiju_pradzia'] = $cell->getFormattedValue();
                } else if('P' == $cell->getColumn()){
                    $array_data[$rowIndex]['studiju_pabaiga'] = $cell->getFormattedValue();
                } else if('Q' == $cell->getColumn()){
                    $array_data[$rowIndex]['lsp_numeris'] = $cell->getFormattedValue();
                } else if('R' == $cell->getColumn()){
                    $array_data[$rowIndex]['ldap'] = $cell->getFormattedValue();
                } else if('S' == $cell->getColumn()){
                    $array_data[$rowIndex]['kalbos_kodas'] = $cell->getFormattedValue();
                } else if('T' == $cell->getColumn()){
                    $array_data[$rowIndex]['statusas'] = $cell->getFormattedValue();
                } else if('U' == $cell->getColumn()){
                    $array_data[$rowIndex]['profilio_kodas'] = $cell->getFormattedValue();
                } else if('V' == $cell->getColumn()){
                    $array_data[$rowIndex]['bibl_filialo_kodas'] = $cell->getFormattedValue();
                } else if('W' == $cell->getColumn()){
                    $array_data[$rowIndex]['adresas_irasytas'] = $cell->getFormattedValue();
                } else if('X' == $cell->getColumn()){
                    $array_data[$rowIndex]['1metai'] = $cell->getFormattedValue();
                } else if('Y' == $cell->getColumn()){
                    $array_data[$rowIndex]['atsitiktiniu_simboliu_seka'] = $cell->getFormattedValue();
                }
            }
        }
        return $array_data;
    }

    function downloader($data, $filename = true, $content = 'application/x-octet-stream')
    {
        // If headers have already been sent, there is no point for this function.
        if(headers_sent()) return false;
        // If $filename is set to true (or left as default), treat $data as a filepath.
        if($filename === true)
        {
            if(!file_exists($data)) return false;
            $data = file_get_contents($data);
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false)
        {
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: '.$content);
            header('Pragma: public');
            header('Content-Length: '.strlen($data));
        }
        else
        {
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: '.$content);
            header('Expires: 0');
            header('Pragma: no-cache');
            header('Content-Length: '.strlen($data));
        }
        // Send file to browser, and terminate script to prevent corruption of data.
        exit($data);
    }

    private function prepareContent($data){
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElement('p-file-20');
        $root = $doc->appendChild($root);

        foreach($data as $person){
            $patron = $doc->createElement('patron-record');
            $patron = $root->appendChild($patron);

            $z303 = $doc->createElement('z303');
            $z303 = $patron->appendChild($z303);

            $z303->appendChild($doc->createElement('record-action', 'A'));
            $z303->appendChild($doc->createElement('match-id-type', '01'));
            $z303->appendChild($doc->createElement('match-id', '0'.$person['ak']));
            $z303->appendChild($doc->createElement('z303-id', '0'.$person['ak']));
            $z303->appendChild($doc->createElement('z303-user-type', 'REG'));
            $z303->appendChild($doc->createElement('z303-con-lng', $person['kalbos_kodas']));
            $z303->appendChild($doc->createElement('z303-alpha', 'L'));
            $z303->appendChild($doc->createElement('z303-first-name', $person['vardas']));
            $z303->appendChild($doc->createElement('z303-last-name', $person['pavarde']));
            $z303->appendChild($doc->createElement('z303-title', $person['statusas']));
            $z303->appendChild($doc->createElement('z303-delinq-1', '00'));
            $z303->appendChild($doc->createElement('z303-delinq-n-1', ''));
            $z303->appendChild($doc->createElement('z303-delinq-3', '00'));
            $z303->appendChild($doc->createElement('z303-delinq-n-3', '+'));
            $z303->appendChild($doc->createElement('z303-budget', ''));
            $z303->appendChild($doc->createElement('z303-profile', $person['profilio_kodas']));
            $z303->appendChild($doc->createElement('z303-ill-library', ''));
            $z303->appendChild($doc->createElement('z303-home-library', $person['bibl_filialo_kodas']));
            $z303->appendChild($doc->createElement('z303-note-1', '+'));
            $z303->appendChild($doc->createElement('z303-ill-total-limit', '0000'));
            $z303->appendChild($doc->createElement('z303-ill-active-limit', '0000'));
            $z303->appendChild($doc->createElement('z303-birth-date', $person['gimimo_data']));
            $z303->appendChild($doc->createElement('z303-export-consent', 'Y'));
            $z303->appendChild($doc->createElement('z303-proxy-id-type', '00'));
            $z303->appendChild($doc->createElement('z303-send-all-letters', 'Y'));
            $z303->appendChild($doc->createElement('z303-plain-html', 'H'));
            $z303->appendChild($doc->createElement('z303-want-sms', 'N'));
            $z303->appendChild($doc->createElement('z303-title-req-limit', '0099'));
            $z303->appendChild($doc->createElement('z303-gender', 'lytis'));
            $z303->appendChild($doc->createElement('z303-birthplace', ''));


            $z304 = $doc->createElement('z304');
            $z304 = $patron->appendChild($z304);

            $z304->appendChild($doc->createElement('record-action', 'A'));
            $z304->appendChild($doc->createElement('z304-id', '0'.$person['ak']));
            $z304->appendChild($doc->createElement('z304-sequence', '01'));
            $z304->appendChild($doc->createElement('z304-address-0', $person['vardas_pavarde']));
            $z304->appendChild($doc->createElement('z304-address-1', $person['gatve']));
            $z304->appendChild($doc->createElement('z304-address-2', $person['miestas']));
            $z304->appendChild($doc->createElement('z304-address-3', ''));
            $z304->appendChild($doc->createElement('z304-address-4', ''));
            $z304->appendChild($doc->createElement('z304-zip', ''));
            $z304->appendChild($doc->createElement('z304-email-address', $person['el_pastas']));
            $z304->appendChild($doc->createElement('z304-telephone', $person['tel']));
            $z304->appendChild($doc->createElement('z304-date-from', $person['adresas_irasytas']));
            $z304->appendChild($doc->createElement('z304-date-to', $person['1metai']));
            $z304->appendChild($doc->createElement('z304-address-type', '01'));
            $z304->appendChild($doc->createElement('z304-telephone-2', ''));
            $z304->appendChild($doc->createElement('z304-telephone-3', ''));
            $z304->appendChild($doc->createElement('z304-telephone-4', ''));


            $z304 = $doc->createElement('z304');
            $z304 = $patron->appendChild($z304);

            $z304->appendChild($doc->createElement('record-action', 'A'));
            $z304->appendChild($doc->createElement('z304-id', '0'.$person['ak']));
            $z304->appendChild($doc->createElement('z304-sequence', '02'));
            $z304->appendChild($doc->createElement('z304-address-0', $person['vardas_pavarde']));
            $z304->appendChild($doc->createElement('z304-address-1', $person['studiju_programa']));
            $z304->appendChild($doc->createElement('z304-address-2', $person['katedra']));
            $z304->appendChild($doc->createElement('z304-address-3', $person['statusas'] == 'Stud.' ? 'Studentas' : 'Dėstytojas'));
            $z304->appendChild($doc->createElement('z304-address-4', ''));
            $z304->appendChild($doc->createElement('z304-zip', ''));
            $z304->appendChild($doc->createElement('z304-email-address', $person['el_pastas']));
            $z304->appendChild($doc->createElement('z304-telephone', $person['tel']));
            $z304->appendChild($doc->createElement('z304-date-from', $person['studiju_pradzia']));
            $z304->appendChild($doc->createElement('z304-date-to', $person['1metai']));
            $z304->appendChild($doc->createElement('z304-address-type', '02'));
            $z304->appendChild($doc->createElement('z304-telephone-2', ''));
            $z304->appendChild($doc->createElement('z304-telephone-3', ''));
            $z304->appendChild($doc->createElement('z304-telephone-4', ''));


            $z305 = $doc->createElement('z305');
            $z305 = $patron->appendChild($z305);

            $z305->appendChild($doc->createElement('record-action', 'A'));
            $z305->appendChild($doc->createElement('z305-id', '0'.$person['ak']));
            $z305->appendChild($doc->createElement('z305-sub-library', 'KTU50'));
            $z305->appendChild($doc->createElement('z305-bor-type', 'DA'));
            $z305->appendChild($doc->createElement('z305-bor-status', '01'));
            $z305->appendChild($doc->createElement('z305-registration-date', '0'.$person['studiju_pradzia']));
            $z305->appendChild($doc->createElement('z305-expiry-date', ''));


            $z305 = $doc->createElement('z305');
            $z305 = $patron->appendChild($z305);

            $z305->appendChild($doc->createElement('record-action', 'A'));
            $z305->appendChild($doc->createElement('z305-id', '0'.$person['ak']));
            $z305->appendChild($doc->createElement('z305-sub-library', 'KTUCB'));
            $z305->appendChild($doc->createElement('z305-bor-type', 'DA'));
            $z305->appendChild($doc->createElement('z305-bor-status', '01'));
            $z305->appendChild($doc->createElement('z305-registration-date', '0'.$person['studiju_pradzia']));
            $z305->appendChild($doc->createElement('z305-expiry-date', ''));


            $z305 = $doc->createElement('z305');
            $z305 = $patron->appendChild($z305);

            $z305->appendChild($doc->createElement('record-action', 'A'));
            $z305->appendChild($doc->createElement('z305-id', '0'.$person['ak']));
            $z305->appendChild($doc->createElement('z305-sub-library', 'KTUIF'));
            $z305->appendChild($doc->createElement('z305-bor-type', 'DA'));
            $z305->appendChild($doc->createElement('z305-bor-status', '01'));
            $z305->appendChild($doc->createElement('z305-registration-date', '0'.$person['studiju_pradzia']));
            $z305->appendChild($doc->createElement('z305-expiry-date', ''));


            $z305 = $doc->createElement('z305');
            $z305 = $patron->appendChild($z305);

            $z305->appendChild($doc->createElement('record-action', 'A'));
            $z305->appendChild($doc->createElement('z305-id', '0'.$person['ak']));
            $z305->appendChild($doc->createElement('z305-sub-library', 'KTUMD'));
            $z305->appendChild($doc->createElement('z305-bor-type', 'DA'));
            $z305->appendChild($doc->createElement('z305-bor-status', '01'));
            $z305->appendChild($doc->createElement('z305-registration-date', '0'.$person['studiju_pradzia']));
            $z305->appendChild($doc->createElement('z305-expiry-date', ''));


            $z308 = $doc->createElement('z308');
            $z308 = $patron->appendChild($z308);

            $z308->appendChild($doc->createElement('record-action', 'A'));
            $z308->appendChild($doc->createElement('z308-key-type', '01'));
            $z308->appendChild($doc->createElement('z308-key-data', '0'.strtoupper($person['ak'])));
            $z308->appendChild($doc->createElement('z308-verification', $person['atsitiktiniu_simboliu_seka']));
            $z308->appendChild($doc->createElement('z308-verification-type', '00'));
            $z308->appendChild($doc->createElement('z308-status', 'AC'));
            $z308->appendChild($doc->createElement('z308-encryption', 'H'));


            $z308 = $doc->createElement('z308');
            $z308 = $patron->appendChild($z308);

            $z308->appendChild($doc->createElement('record-action', 'A'));
            $z308->appendChild($doc->createElement('z308-key-type', '02'));
            $z308->appendChild($doc->createElement('z308-key-data', strtoupper($person['ak'])));
            $z308->appendChild($doc->createElement('z308-verification', $person['atsitiktiniu_simboliu_seka']));
            $z308->appendChild($doc->createElement('z308-verification-type', '00'));
            $z308->appendChild($doc->createElement('z308-status', 'AC'));
            $z308->appendChild($doc->createElement('z308-encryption', 'H'));


            $z308 = $doc->createElement('z308');
            $z308 = $patron->appendChild($z308);

            $z308->appendChild($doc->createElement('record-action', 'A'));
            $z308->appendChild($doc->createElement('z308-key-type', '07'));
            $z308->appendChild($doc->createElement('z308-key-data', $person['ldap']));
            $z308->appendChild($doc->createElement('z308-verification', substr($person['ak'], -4)));
            $z308->appendChild($doc->createElement('z308-verification-type', '00'));
            $z308->appendChild($doc->createElement('z308-status', 'AC'));
            $z308->appendChild($doc->createElement('z308-encryption', 'H'));


            if($person['lsp_numeris']){
                $z308 = $doc->createElement('z308');
                $z308 = $patron->appendChild($z308);

                $z308->appendChild($doc->createElement('record-action', 'A'));
                $z308->appendChild($doc->createElement('z308-key-type', '08'));
                $z308->appendChild($doc->createElement('z308-key-data', strtoupper($person['lsp_numeris'])));
                $z308->appendChild($doc->createElement('z308-verification', $person['atsitiktiniu_simboliu_seka']));
                $z308->appendChild($doc->createElement('z308-verification-type', '00'));
                $z308->appendChild($doc->createElement('z308-status', 'AC'));
                $z308->appendChild($doc->createElement('z308-encryption', 'H'));
            }
        }

        $this->content = $doc->saveXML();
    }

    private function downloadXML(){
        ob_clean();
        $this->downloader($this->content, 'database.xml', 'application/xml');
    }

    function makeXML(){
        $this->initExcelFile();
        $excelArray = $this->getArray();
        $this->prepareContent($excelArray);
        $this->downloadXML();
    }
}