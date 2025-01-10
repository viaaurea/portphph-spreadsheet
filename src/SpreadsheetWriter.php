<?php
/*
The MIT License (MIT)

Copyright (c) 2015 PortPHP

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */
namespace Port\Spreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Port\Writer;

/**
 * Writes to an Spreadsheet file
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class SpreadsheetWriter implements Writer
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $prependHeaderRow;

    /**
     * @var int
     */
    protected $row = 1;

    /**
     * @var null|string
     */
    protected $sheet;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param \SplFileObject $file             File
     * @param string         $sheet            Sheet title (optional)
     * @param string         $type             Spreadsheet file type (defaults to Xlsx)
     * @param bool           $prependHeaderRow
     */
    public function __construct(\SplFileObject $file, $sheet = null, $type = 'Xlsx', $prependHeaderRow = false)
    {
        $this->filename         = $file->getPathname();
        $this->sheet            = $sheet;
        $this->type             = $type;
        $this->prependHeaderRow = $prependHeaderRow;
    }

    /**
     * Wrap up the writer after all items have been written
     *
     * @return void Any returned value is ignored.
     */
    public function finish(): void
    {
        $writer = IOFactory::createWriter($this->spreadsheet, $this->type);
        $writer->save($this->filename);
    }

    /**
     * Prepare the writer before writing the items
     *
     * @return void Any returned value is ignored.
     */
    public function prepare(): void
    {
        $reader = IOFactory::createReader($this->type);
        if ($reader->canRead($this->filename)) {
            $this->spreadsheet = $reader->load($this->filename);
        } else {
            $this->spreadsheet = new Spreadsheet();
        }

        if (null !== $this->sheet) {
            if (!$this->spreadsheet->sheetNameExists($this->sheet)) {
                $this->spreadsheet->createSheet()->setTitle($this->sheet);
            }
            $this->spreadsheet->setActiveSheetIndexByName($this->sheet);
        }
    }

    /**
     * Write one data item
     *
     * @param array $item The data item with converted values
     *
     * @return void Any returned value is ignored.
     */
    public function writeItem(array $item): void
    {
        $count = count($item);

        if ($this->prependHeaderRow && 1 === $this->row) {
            $headers = array_keys($item);

            for ($i = 0; $i < $count; $i++) {
                $this->spreadsheet->getActiveSheet()->setCellValue([$i + 1, $this->row], $headers[$i]); // fixed removal of `setCellValueByColumnAndRow`
            }
            $this->row++;
        }

        $values = array_values($item);

        for ($i = 0; $i < $count; $i++) {
            $this->spreadsheet->getActiveSheet()->setCellValue([$i + 1, $this->row], $values[$i]); // fixed removal of `setCellValueByColumnAndRow`
        }

        $this->row++;
    }
}
