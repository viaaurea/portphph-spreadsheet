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

use Port\Reader\ReaderFactory;

/**
 * Factory that creates SpreadsheetReaders
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class SpreadsheetReaderFactory implements ReaderFactory
{
    protected ?int $activeSheet;

    protected ?int $headerRowNumber;

    /**
     * @param int $headerRowNumber
     * @param int $activeSheet
     */
    public function __construct(?int $headerRowNumber = null, ?int $activeSheet = null)
    {
        $this->headerRowNumber = $headerRowNumber;
        $this->activeSheet     = $activeSheet;
    }

    /**
     * @param \SplFileObject $file
     *
     * @return SpreadsheetReader
     */
    public function getReader(\SplFileObject $file): SpreadsheetReader
    {
        return new SpreadsheetReader($file, $this->headerRowNumber, $this->activeSheet);
    }
}
