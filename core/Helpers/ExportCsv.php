<?php

namespace Core\Helpers;

class ExportCsv
{
    public $filename = '';

    // file extension
    const FILE_EXTENSION = '.csv';

    public function __construct($filename = '')
    {
        if (empty($filename)) {
            $filename = 'export_' . date('YmdHis');
        }
        $this->filename = $filename;
    }

    /**
     * @param $dataHeader
     * @param $dataExport
     * @param bool $isSJIS
     * @param string $delimiter
     */
    public function export($dataHeader, $dataExport, bool $isSJIS = false, string $delimiter = ',')
    {
        $filename = $this->filename . self::FILE_EXTENSION;

        if ($isSJIS) {
            $filename = $this->_setSJIS($filename);
            if (!empty($dataHeader)) {
                $dataHeader = $this->_setHeaderSJIS($dataHeader);
            }
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $csvFile = fopen('php://output', 'w');
        } else { // UTF-8 BOM
            header('Content-Encoding: UTF-8');
            header('Content-type: application/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $csvFile = fopen('php://output', 'w');
            // Insert the UTF-8 BOM in the file
            fputs($csvFile, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        }

        if (!empty($dataHeader)) {
            $this->_putItemCsv($csvFile, $dataHeader, $delimiter);
        }

        $listKeys = array_keys($dataHeader);

        if (!empty($dataExport)) {
            foreach ($dataExport as $item) {
                $tmp = [];

                foreach ($listKeys as $field) {
                    if (!is_null($field) && isset($item[$field])) {
                        $tmp[] = $isSJIS ? $this->_setSJIS($item[$field]) : $item[$field];
                    }
                }

                if (!empty($tmp)) {
                    $this->_putItemCsv($csvFile, $tmp, $delimiter);
                }
            }
        }

        fclose($csvFile);
        exit;
    }

    /**
     * @param $filename
     * @return bool|false|string|string[]|null
     */
    protected function _setSJIS($filename)
    {
        return mb_convert_encoding($filename, 'SJIS', 'UTF-8');
    }

    /**
     * @param $headers
     * @return array
     */
    protected function _setHeaderSJIS($headers): array
    {
        $data = [];
        foreach ($headers as $key => $header) {
            $data[$key] = mb_convert_encoding($header, 'SJIS', 'UTF-8');
        }
        return $data;
    }

    /**
     * @param $handle
     * @param $item
     * @param $delimiter
     * @return false|int
     */
    protected function _putItemCsv($handle, $item, $delimiter)
    {
        $item = array_map(function ($value) {
            return '"' . $value . '"';
        }, $item);

        return fputs($handle, implode($delimiter, $item) . "\r\n");
    }
}
