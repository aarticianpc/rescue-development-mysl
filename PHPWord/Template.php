<?php
/**
 * PHPWord
 *
 * Copyright (c) 2011 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 010 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    Beta 0.6.3, 08.07.2011
 */


/**
 * PHPWord_DocumentProperties
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 2009 - 2011 PHPWord (http://www.codeplex.com/PHPWord)
 */
class PHPWord_Template {
    
    /**
     * ZipArchive
     * 
     * @var ZipArchive
     */
    private $_objZip;
    
    /**
     * Temporary Filename
     * 
     * @var string
     */
    private $_tempFileName;
    
    /**
     * Document XML
     * 
     * @var string
     */
    private $_documentXML;
    
    
    /**
     * Create a new Template Object
     * 
     * @param string $strFilename
     */
    public function __construct($strFilename) {
        $path = dirname($strFilename);
        $this->_tempFileName = $path.DIRECTORY_SEPARATOR.time().'.docx';
        
        copy($strFilename, $this->_tempFileName); // Copy the source File to the temp File

        $this->_objZip = new ZipArchive();
        $this->_objZip->open($this->_tempFileName);
        
        $this->_documentXML = $this->_objZip->getFromName('word/document.xml');
    }
    
    /**
     * Set a Template value
     * 
     * @param mixed $search
     * @param mixed $replace
     */
    public function setValue($search, $replace) {
        if(substr($search, 0, 2) !== '${' && substr($search, -1) !== '}') {
            $search = '${'.$search.'}';
        }
        
        if(!is_array($replace)) {
            $replace = utf8_encode($replace);
        }
        $this->_documentXML = str_replace($search, $replace, $this->_documentXML);
    }
    
    /**
     * Save Template
     * 
     * @param string $strFilename
     */
    public function save($strFilename) {
        if(file_exists($strFilename)) {
            unlink($strFilename);
        }
        
        $this->_objZip->addFromString('word/document.xml', $this->_documentXML);
        
        // Close zip file
        if($this->_objZip->close() === false) {
            throw new Exception('Could not close zip file.');
        }
        
        rename($this->_tempFileName, $strFilename);
    }

    public  function setValueAdvanced($search_replace)
    {
        
        $this->_documentXML = $this->setValueForPartAdvanced($this->_documentXML, $search_replace);

        echo $this->_documentXML;
    }
    public  function setValueForPartAdvanced($documentPartXML, $search_replace)
    {
        $pattern = '/<w:t>(.*?)<\/w:t>/';
        $rplStringBeginOffcetsStack = array();
        $rplStringEndOffcetsStack = array();
        $rplCleanedStrings = array();
        $stringsToClean = array();
        preg_match_all($pattern, $documentPartXML, $words, PREG_OFFSET_CAPTURE);

        $bux_founded = false;
        $searching_started = false;
        foreach($words[1] as $key_of_words => $word)
        {
            $exploded_chars = str_split($word[0]);
            foreach($exploded_chars as $key_of_chars => $char)
            {
                if ($bux_founded)
                {
                    if ($searching_started)
                    {
                        if ($char == "}")
                        {
                            $bux_founded = false;
                            $searching_started = false;
                            array_push($rplStringEndOffcetsStack, ($word[1]+mb_strlen($word[0])+6));
                        }
                    }
                    else
                    {
                        if ($char == "{")
                        {
                            $searching_started = true;
                        }
                        else
                        {
                            $bux_founded = false;
                            array_pop($rplStringBeginOffcetsStack);
                        }
                    }
                }
                else
                {
                    if ($char == "$")
                    {
                        $bux_founded = true;
                        array_push($rplStringBeginOffcetsStack, $word[1]-5);
                    }
                }
            }
        }
        for($index=0; $index<count($rplStringEndOffcetsStack); $index++)
        {
            $string_to_clean = substr($documentPartXML, $rplStringBeginOffcetsStack[$index], ($rplStringEndOffcetsStack[$index]-$rplStringBeginOffcetsStack[$index]));
            array_push($stringsToClean, $string_to_clean);
            preg_match_all($pattern, $string_to_clean, $words_to_concat);
            $cleaned_string = implode("", $words_to_concat[1]);
            $cleaned_string = preg_replace('/[${}]+/', '', $cleaned_string);
            array_push($rplCleanedStrings, $cleaned_string);
        }
        for ($index=0; $index<count($rplCleanedStrings); $index++)
        {
            foreach($search_replace as $key_search => $replace)
            {
                if ($rplCleanedStrings[$index] == $key_search)
                {
                    $documentPartXML = str_replace($stringsToClean[$index], "<w:t>".$replace."</w:t>", $documentPartXML);
                    break;
                }
            }
        }
        return $documentPartXML;
    }
}
?>