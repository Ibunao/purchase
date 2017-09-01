<?php
namespace frontend\helpers;
class IoXls
{

    public $name = 'xls-Excel文件';

    public function download($fname = 'data', $data = null, $mimeType = 'application/force-download')
    {
        //检验header头是否已经发送
        if (headers_sent($file, $line)) {
            echo 'Header already sent @ ' . $file . ':' . $line;
            exit();
        }


        //header('Cache-Control: no-cache;must-revalidate'); //fix ie download bug
        header('Pragma: no-cache, no-store');
        header("Expires: Wed, 26 Feb 1997 08:21:57 GMT");
        if (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE')) {
            $fname = urlencode($fname);
            header('Content-type: ' . $mimeType);
        } else {
            header('Content-type: ' . $mimeType . ';charset=utf-8');

        }

        header("Content-Disposition: attachment; filename=\"" . $fname . '"');
        //header( "Content-Description: File Transfer");

        if ($data) {
            header('Content-Length: ' . strlen($data));
            echo $data;
            exit();
        }
    }


    public function export_begin($keys, $type, $count)
    {

        $this->download($type . '.xlsx');
        echo ' 
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style>
                td{vnd.ms-excel.numberformat:@}
            </style>
        </head>';
        echo '<table width="100%" border="1">';
        echo '<tr><th filter=all>' . implode('</th><th filter=all>', $keys) . "</th></tr>\r\n";
        flush();
    }

    public function export_rows($rows)
    {

        foreach ($rows as $row) {
            echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\r\n";
        }
        flush();
    }

    public function export_finish()
    {
        echo '</table>';
        flush();
    }

}
