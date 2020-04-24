<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use \PhpOffice\PhpWord\TemplateProcessor;

class ExtTemplateProcessor extends TemplateProcessor {
	/**
     * Finds parts of broken macros and sticks them together.
     * Macros, while being edited, could be implicitly broken by some of the word processors.
     *
     * @param string $documentPart The document part in XML representation.
     *
     * @return string
     */
    protected function fixBrokenMacros($documentPart)
    {
        $fixedDocumentPart = $documentPart;

        $fixedDocumentPart = preg_replace_callback(
            '|\$[^{]*\{[^}]*\}|U',
            function ($match) {
                if(preg_match('/\$__/xi', $match[0])){
                    return $match[0];
                } else {
                    return strip_tags($match[0]);
                }
            },
            $fixedDocumentPart
        );

        return $fixedDocumentPart;
    }
}
