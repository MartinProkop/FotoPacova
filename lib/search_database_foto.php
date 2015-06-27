<?php

require ("./Debug.php");
Debug::enable(Debug::DETECT, './logs/php_error.log');
//// error_reporting(E_ALL);
error_reporting(E_ALL ^ ~E_STRICT);
//// ^ E_NOTICE ^ E_WARNING
require ("./dibi/dibi.php");
require ("./db.php");
require ("./funkce.php");

//get the q parameter from URL
$q = $_GET["q"];

if (strlen($q) > 0) {
    $hint = "";
    $i = 0;

    $query = dibi::query("SELECT * FROM [pf_databaze] WHERE ([id_zaznamu] LIKE %s", '%' . $_GET["q"] . '%', " OR [klicova_slova] LIKE %s", '%' . $_GET["q"] . '%', " OR [popis] LIKE %s", '%' . $_GET["q"] . '%', ") AND [zverejnit] = %s", "ano", "ORDER BY %by", "id", "DESC");
    while ($row = $query->fetch()) {
        if ($i == 5) {
            $hint = $hint . "<br />... v databázi je více než 5 odpovídajících položek";
            break;
        }

        if ($hint == "") {
            $hint = "<a href=\"./index.php?id=vyhledavani&ad=$row[id]\">#" . $row[id_zaznamu] . " - " . echo_lokalita_of_foto_db($row[lokalita]) . "</a>";
        } else {
            $hint = $hint . "<br /><a href=\"./index.php?id=vyhledavani&ad=$row[id]\">#" . $row[id_zaznamu] . " - " . echo_lokalita_of_foto_db($row[lokalita])  . "</a>";
        }
        $i++;
    }
}



if ($hint == "") {
    $response = "Nenalezen žádný záznam";
} else {
    $response = $hint;
}


echo $response;
?> 