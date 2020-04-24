<?php

function getLastInsertedId($resource) {
    sqlsrv_next_result($resource); 
    sqlsrv_fetch($resource); 
    return sqlsrv_get_field($resource, 0);
}

function mssql_escape($str)
{
    if(get_magic_quotes_gpc())
    {
        $str= stripslashes($str);
    }
    return str_replace("'", "''", $str);
}