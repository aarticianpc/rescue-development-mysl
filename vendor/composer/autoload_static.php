<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitccaff143fe07f76d592c22109b7d8717
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\Validator\\' => 15,
            'Zend\\Stdlib\\' => 12,
            'Zend\\Escaper\\' => 13,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Common\\' => 17,
        ),
        'J' => 
        array (
            'Jupitern\\Docx\\' => 14,
        ),
        'D' => 
        array (
            'DocxMerge\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\Validator\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-validator/src',
        ),
        'Zend\\Stdlib\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-stdlib/src',
        ),
        'Zend\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-escaper/src',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Common\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/common/src/Common',
        ),
        'Jupitern\\Docx\\' => 
        array (
            0 => __DIR__ . '/..' . '/jupitern/docx/src',
        ),
        'DocxMerge\\' => 
        array (
            0 => __DIR__ . '/..' . '/krustnic/docx-merge/src',
        ),
    );

    public static $classMap = array (
        'DocxMerge\\DocxMerge' => __DIR__ . '/..' . '/krustnic/docx-merge/src/DocxMerge.php',
        'DocxMerge\\DocxMerge\\Docx' => __DIR__ . '/..' . '/krustnic/docx-merge/src/DocxMerge/Docx.php',
        'DocxMerge\\DocxMerge\\Prettify' => __DIR__ . '/..' . '/krustnic/docx-merge/src/DocxMerge/Prettify.php',
        'DocxMerge\\libraries\\TbsZip' => __DIR__ . '/..' . '/krustnic/docx-merge/src/libraries/TbsZip.php',
        'PclZip' => __DIR__ . '/..' . '/pclzip/pclzip/pclzip.lib.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitccaff143fe07f76d592c22109b7d8717::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitccaff143fe07f76d592c22109b7d8717::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitccaff143fe07f76d592c22109b7d8717::$classMap;

        }, null, ClassLoader::class);
    }
}
