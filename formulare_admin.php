<?php
require ("./lib/Debug.php");
Debug::enable(Debug::DETECT, './logs/php_error.log');
//// error_reporting(E_ALL);
error_reporting(E_ALL ^ ~E_STRICT);
//// ^ E_NOTICE ^ E_WARNING
require ("./lib/dibi/dibi.php");
session_start();
session_name("jmeno");
session_name("heslo");
require ("./lib/db.php");
require ("./lib/funkce.php");
require ("./lib/prihlaseni_plugin.php");
require ("./lib/PHPMailer-master/PHPMailerAutoload.php");
?>
<!DOCTYPE html>
<html lang="cs">
    <head>

        <title>FOTOPACOV</title>

        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

        <!-- základ -->
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="martin prokop" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="Pijem, jíme, hodnotíme, ">
        <meta name="description" content="">
        <link type="" href="html/favicon.png" rel="shortcut icon">
        <link href="./img/favicon.gif" rel="icon" type="image/gif" />
        <meta name="robots" content="index,follow,archive" />
        <meta name="googlebot" content="snippet,archive" />
        <script type="text/javascript" src="./lib/script.js"></script>

        <!-- styl -->
        <?php
        require ("./lib/check_mobile.php");
        if (check_mobile()) {
            echo "<link href=\"./lib/style_mobile.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />";
        } else {
            echo "<link href=\"./lib/style.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />";
        }
        ?>

        <!-- validate -->
        <script type="text/javascript" src="./lib/fancybox/lib/jquery-1.10.1.min.js"></script>
        <script src="./lib/validate/PJH/jquery.validate.js"></script>
        <script type="text/javascript" src="./lib/validate/PJH/messages_cs.js"></script>

        <!-- inicializace validace -->
        <script type="text/javascript">
            $(document).ready(function () {
                $("#load").hide();
                $("#formul").validate();
            });
            $(document).ready(function () {
                $("#formul").submit(function ()
                {
                    if ($("#formul").valid()) {
                        $('#submit1').toggle();
                        $("#load").show();
                    }
                });
            });
        </script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <link rel="stylesheet" href="./lib/jquery-ui-1.11.2.custom/jquery-ui.css">
        <script src="./lib/jquery-ui-1.11.2.custom/jquery.ui.datepicker-cs.js"></script>
        <script>


            $(function () {
                $("#datumyg").datepicker();

            });
        </script>       


    </head>
    <body style="background: #f0f0f0;">
        <div id="okno_form">
            <?php
            if (login_check()) {
                if ($_GET[id] == "novyclanek") {
                    echo "<h1>Nový článek</h1>";

                    $no_error_foto = true;

                    //$no_error_foto_aspon_neco = true;
                    $no_error_foto_aspon_neco = false;

                    $query_upfoto = dibi::query('SELECT * FROM [pf_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto');
                    if ($query_upfoto->count() > 0)
                        $no_error_foto_aspon_neco = false;

                    if ($_GET[hash] == "")
                        $_GET[hash] = time();

                    if ($_GET[action] == "send") {
                        if (count($_FILES['fileToUpload']['name']) > 0 && $_FILES['fileToUpload']['name'][0] != "") {
                            for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
                                if ($_FILES["fileToUpload"]["size"][$i] > 1000000) {
                                    echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                                    $no_error_foto = false;
                                } else {
                                    $timestamp = time();
                                    $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'][$i];
                                    $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                        echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                        $no_error_foto = false;
                                    } else {
                                        move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");

                                        $arr_temp = array('id_hash' => $_GET[hash], 'znacka' => "novefoto", 'value1' => $nazev_souboru, 'value2' => $cil);
                                        dibi::query('INSERT INTO [pf_temp]', $arr_temp);
                                        $no_error_foto_aspon_neco = false;
                                    }
                                }
                            }
                        }

                        //uložime
                        if ($no_error_foto) {
                            // 
                            $arr_akt = array('date' => time(), 'id_relace' => $_POST['selectfotka'], 'typ' => "fotka", 'nazev' => $_POST['nazev'], 'obsah' => $_POST['textarea1'], 'zverejnit' => 'ano');
                            dibi::query('INSERT INTO [pf_clanky]', $arr_akt);

                            $query_id_akt = dibi::query('SELECT * FROM [pf_clanky] ORDER BY %by', 'id', 'DESC');
                            $row_id_akt = $query_id_akt->fetch();

                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pf_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                            $i = 1;
                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./obrazky/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                $arr_fotka = array('nazev' => $_POST['nazev'], 'cil' => $newname, 'typ' => "clanek", 'id_cil' => $row_id_akt['id'], 'poradi' => $i, 'popis' => $_POST['nazev'], 'date' => time(), 'zverejnit' => "ano");
                                dibi::query('INSERT INTO [pf_fotky]', $arr_fotka);

                                dibi::query('DELETE FROM [pf_temp] WHERE [id] = %i', $row_upfoto[id]);

                                $i++;
                            }

                            create_udalost("clanek", $row_id_akt['id'], $_POST['nazev']);


                            echo "<div class=\"msg information\"><h2>Článek byla přidán!</h2></div>";
                            ?>
                            <script>

                                close_fancybox_redirect_parent('./administrace.php?id=clanky', 3000);
                            </script>
                            <?php
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        ?>        
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=novyclanek&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">

                                <dt><label for="nazev">Název článku *</label></dt>
                                <dd><input type="text" class="" id="nazev" name="nazev" value="<?php echo $_POST[nazev]; ?>" placeholder="Název článku" required></dd>                                

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pf_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[value2] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                }
                                ?>

                                <?php
                                if ($_POST[textarea1] == "" || $no_error_foto == false) {
                                    ?>
                                    <dt><label for="fileToUpload">Fotografie <?php if ($no_error_foto_aspon_neco) echo "*"; ?></label><br /><a href="#" onclick="add_more_foto()">přidat další fotografii</a></dt>
                                    <dd>
                                        <div id="addfoto">
                                            <input type="file" class="" name="fileToUpload[]" id="fileToUpload" <?php if ($no_error_foto_aspon_neco) echo "required"; ?>>
                                        </div>
                                    </dd>                        
                                    <?php
                                }
                                ?>


                                <dt><label for="textarea1">Text *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Text článku" required><?php echo $_POST[textarea1]; ?></textarea></dd>


                                <div class="cara"> </div>     

                                <dt><label for="select">Navázat na fotku</label></dt>
                                <dd>
                                    <select size="5" id="selectfotka" name="selectfotka" class="select" required>
                                        <option value="0">--- žádnou ---</option>
                                        <?php
                                        $query_restaurace = dibi::query("SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
                                        while ($row_restaurace = $query_restaurace->fetch()) {
                                            echo "<option value=\"$row_restaurace[id]\">$row_restaurace[id_zaznamu]</option>";
                                        }
                                        ?>
                                    </select>                                   
                                </dd>                                

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat článek"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "upravitclanek") {
                    echo "<h1>Upravit článek</h1>";
                    if ($_GET[action] == "send") {
                        $arr_akt = array('date' => time(), 'nazev' => $_POST['nazev'], 'obsah' => $_POST['textarea1'], 'zverejnit' => 'ano');
                        dibi::query('UPDATE [pf_clanky] SET', $arr_akt, 'WHERE [id] = %i', $_GET[ad]);


                        echo "<div class=\"msg information\"><h2>Aktualita byla upravena!</h2></div>";
                        ?>
                        <script>

                            close_fancybox_redirect_parent('./administrace.php?id=clanky', 3000);
                        </script>
                        <?php
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        $query = dibi::query('SELECT * FROM [pf_clanky] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        ?>   
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravitclanek&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">

                                <dt><label for="nazev">Název článku *</label></dt>
                                <dd><input type="text" class="" id="nazev" name="nazev" value="<?php echo $row[nazev]; ?>" placeholder="Název článku" required></dd>                                
                                <?php
                                echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label></dt>"
                                . "<dd>";
                                get_foto_of_clanek($row[id]);
                                echo "</dd>";
                                ?>                            

                                <dt><label for="textarea1">Text *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Text článku" required><?php echo $row[obsah]; ?></textarea></dd>

                                <div class="cara"> </div>     

                                <dt><label for="select">Navázat na fotku</label></dt>
                                <dd>
                                    <select id="selectfotka" size="5" name="selectfotka" class="select" required>
                                        <option value="0">--- žádnou ---</option>
                                        <?php
                                        $query_restaurace = dibi::query("SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
                                        while ($row_restaurace = $query_restaurace->fetch()) {
                                            if ($row_restaurace[id] == $row[id_relace]) {
                                                echo "<option value=\"$row_restaurace[id]\" selected>$row_restaurace[id_zaznamu]</option>";
                                            } else {
                                                echo "<option value=\"$row_restaurace[id]\">$row_restaurace[id_zaznamu]</option>";
                                            }
                                        }
                                        ?>
                                    </select>                                   
                                </dd>                                


                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Upravit článek"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "novafotografie") {
                    echo "<h1>Nová fotografie</h1>";

                    $no_error_foto = true;

                    //$no_error_foto_aspon_neco = true;
                    $no_error_foto_aspon_neco = false;

                    $query_upfoto = dibi::query('SELECT * FROM [pf_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto');
                    if ($query_upfoto->count() > 0)
                        $no_error_foto_aspon_neco = false;

                    if ($_GET[hash] == "")
                        $_GET[hash] = time();

                    if ($_GET[action] == "send") {
                        if (count($_FILES['fileToUpload']['name']) > 0 && $_FILES['fileToUpload']['name'][0] != "") {
                            for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
                                if ($_FILES["fileToUpload"]["size"][$i] > 3000000) {
                                    echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 3 MB.</p></div>";
                                    $no_error_foto = false;
                                } else {
                                    $timestamp = time();
                                    $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'][$i];
                                    $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                        echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                        $no_error_foto = false;
                                    } else {
                                        move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");

                                        $arr_temp = array('id_hash' => $_GET[hash], 'znacka' => "novefoto", 'value1' => $nazev_souboru, 'value2' => $cil);
                                        dibi::query('INSERT INTO [pf_temp]', $arr_temp);
                                        $no_error_foto_aspon_neco = false;
                                    }
                                }
                            }
                        }
                        //uložime
                        if ($no_error_foto) {
                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pf_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');

                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./databaze_fotografii/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                dibi::query('DELETE FROM [pf_temp] WHERE [id] = %i', $row_upfoto[id]);
                            }

                            $lokalita = $_POST['lokalita1'] . "," . $_POST['lokalita2'] . "," . $_POST['lokalita3'] . "," . $_POST['lokalita4'] . "," . $_POST['lokalita5'];

                            $arr_akt = array('date_zverejneni' => time(), 'date_uprava' => time(), 'cil' => $newname, 'zadavatel_admin' => $_SESSION['jmeno'], 'zadavatel_admin_uprava' => $_SESSION['jmeno'], 'zverejnit' => 'skryt', 'id_zaznamu' => $_POST['id_zaznamu'], 'lokalita' => $lokalita, 'cislo_popisne' => $_POST['cp'], 'udalost' => $_POST['udalost'], 'autorvydavatel' => $_POST['autor'], 'datace_1' => $_POST['datum1'], 'datace_2' => $_POST['datum2'], 'typmedia' => $_POST['typ'], 'skladovano' => $_POST['skladovano'], 'gps' => $_POST['gps'], 'gpsor' => $_POST['gpsor'], 'popis' => $_POST['popis'], 'klicova_slova' => $_POST['klicovaslova']);
                            dibi::query('INSERT INTO [pf_databaze]', $arr_akt);

                            $query_id_akt = dibi::query('SELECT * FROM [pf_databaze] ORDER BY %by', 'id', 'DESC');
                            $row_id_akt = $query_id_akt->fetch();

                            echo "<div class=\"msg information\"><h2>Fotografie byla přidána!</h2></div>";
                            ?>
                            <script>

                                close_fancybox_redirect_parent('./administrace.php?id=databaze', 3000);
                            </script>
                            <?php
                        }
                    }
                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        ?>             
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=novafotografie&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">                             

                                <dt><label for="id_zaznamu">ID záznamu *</label></dt>
                                <dd><input type="text" class="small" id="id_zaznamu" name="id_zaznamu" value="<?php echo $_POST[id_zaznamu]; ?>" placeholder="ID záznamu" required></dd>                                  

                                <dt><label for="lokalita">Lokalita</label></dt>
                                <dd>
                                    <select id="lokalita" name="lokalita1" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[lokalita] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita2" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[lokalita] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita3" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[lokalita] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita4" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[lokalita] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita5" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[lokalita] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd> 

                                <dt><label for="cp">Číslo popisné</label></dt>
                                <dd><input type="text" class="small" id="cp" name="cp" value="<?php echo $_POST[cp]; ?>" placeholder="Číslo popisné"></dd>                                 

                                <dt><label for="udalost">Událost</label></dt>
                                <dd><input type="text" class="" id="udalost" name="udalost" value="<?php echo $_POST[udalost]; ?>" placeholder="Událost"></dd>     

                                <dt><label for="autor">Autor / vydavatel</label></dt>
                                <dd>
                                    <select id="autor" name="autor" size="1" class="select">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_vydavatel] ORDER BY %by', "vydavatel", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[autor] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[vydavatel]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[vydavatel]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                                 

                                <dt><label for="datum1">Datum jedna</label></dt>
                                <dd><input type="text" class="small" id="datum1" value="<?php echo $_POST['datum1']; ?>" name="datum1" maxlength="4" minlength="4" number="true"></dd>

                                <!--
                                <input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="" step="0.1" oninput="jidlovalue.value=value" /><output id="jidlovalue" class="range_output"></output>
                                -->

                                <dt><label for="datum2">Datum dva</label></dt>
                                <dd><input type="text" class="small" id="datum2" value="<?php echo $_POST['datum2']; ?>" name="datum2" maxlength="4" minlength="4" number="true"></dd>

                                <dt><label for="typ">Typ média *</label></dt>
                                <dd>
                                    <select id="typ" name="typ" size="1" class="select" required>
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_typmedia] ORDER BY %by', "typmedia", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[typ] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[typmedia]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[typmedia]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                                 

                                <dt><label for="skladovano">Archiv *</label></dt>
                                <dd>
                                    <select id="skladovano" name="skladovano" size="1" class="select" required>
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_archiv] ORDER BY %by', "archiv", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($_POST[skladovano] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[archiv]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[archiv]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                                   

                                <dt><label for="GPS">GPS</label></dt>
                                <dd><input type="text" class="" id="gps" name="gps" value="<?php echo $_POST[gps]; ?>" placeholder="GPS"></dd>

                                <dt><label for="GPSor">GPS: marker nebo polygon?</label></dt>
                                <dd>
                                    <select id="gpsor" name="gpsor" size="1" class="select">
                                        <option value="marker">marker</option>
                                        <option value="polygon">polygon</option>
                                    </select>
                                </dd>    

                                <dt><label for="klicovaslova">Klíčová slova *</label></dt>
                                <dd><textarea id="klicovaslova" name="klicovaslova" placeholder="Klíčová slova" required><?php echo $_POST[klicovaslova]; ?></textarea></dd>     

                                <dt><label for="popis">Popis</label></dt>
                                <dd><textarea id="popis" name="popis" placeholder="Popis"><?php echo $_POST[popis]; ?></textarea></dd>                                

                                <dt><label for="fileToUpload">Fotografie *</dt>
                                    <dd><input type="file" class="" name="fileToUpload[]" id="fileToUpload" required></dd>                        

                                    <dt><label for="">(*) označuje povinné položky</label></dt>
                                    <dd><input type="submit" class="button" id="submit1" value="Přidat fotografii"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "upravitfotografie") {
                    echo "<h1>Upravit fotografii</h1>";
                    if ($_GET[action] == "send") {

                        $lokalita = $_POST['lokalita1'] . "," . $_POST['lokalita2'] . "," . $_POST['lokalita3'] . "," . $_POST['lokalita4'] . "," . $_POST['lokalita5'];
                        $arr_akt = array('date_uprava' => time(), 'zadavatel_admin_uprava' => $_SESSION['jmeno'], 'id_zaznamu' => $_POST['id_zaznamu'], 'lokalita' => $lokalita, 'cislo_popisne' => $_POST['cp'], 'udalost' => $_POST['udalost'], 'autorvydavatel' => $_POST['autor'], 'datace_1' => $_POST['datum1'], 'datace_2' => $_POST['datum2'], 'typmedia' => $_POST['typ'], 'skladovano' => $_POST['skladovano'], 'gps' => $_POST['gps'], 'gpsor' => $_POST['gpsor'], 'popis' => $_POST['popis'], 'klicova_slova' => $_POST['klicovaslova']);

                        dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [id] = %i', $_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Fotografie byla upravena!</h2></div>";
                        ?>
                        <script>

                            close_fancybox_redirect_parent('./administrace.php?id=databaze', 3000);
                        </script>
                        <?php
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        ?>             
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravitfotografie&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">                          
                                <dt><label for="id_zaznamu">ID záznamu *</label></dt>
                                <dd><input type="text" class="small" id="id_zaznamu" name="id_zaznamu" value="<?php echo $row[id_zaznamu]; ?>" placeholder="ID záznamu" required></dd>                                  

                                <?php
                                $pole = explode(",", $row[lokalita]);
                                ?>

                                <dt><label for="lokalita">Lokalita</label></dt>
                                <dd>
                                    <select id="lokalita" name="lokalita1" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($pole[0] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita2" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($pole[1] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita3" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($pole[2] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita4" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($pole[3] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select id="lokalita" name="lokalita5" size="1" class="select small">
                                        <option value="0">--- nevím ---</option>";
                                        <?php
                                        $lokality = "";
                                        $querylok = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($pole[4] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[lokalita]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[lokalita]</option>";
                                            }
                                        }
                                        ?>
                                    </select>                                    
                                </dd>                                 

                                <dt><label for="cp">Číslo popisné</label></dt>
                                <dd><input type="text" class="small" id="cp" name="cp" value="<?php echo $row[cislo_popisne]; ?>" placeholder="Číslo popisné"></dd>                                 

                                <dt><label for="udalost">Událost</label></dt>
                                <dd><input type="text" class="" id="udalost" name="udalost" value="<?php echo $row[udalost]; ?>" placeholder="Událost"></dd>                                   

                                <dt><label for="autor">Autor / vydavatel</label></dt>
                                <dd>
                                    <select id="autor" name="autor" size="1" class="select">
                                        <option value="0">--- nevím ---</option>
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_vydavatel] ORDER BY %by', "vydavatel", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($row[autorvydavatel] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[vydavatel]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[vydavatel]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                                 

                                <dt><label for="datum1">Datum jedna</label></dt>
                                <dd><input type="text" class="small" id="datum1" value="<?php echo $row['datace_1']; ?>" name="datum1" maxlength="4" minlength="4" number="true"></dd>

                                <dt><label for="datum2">Datum dva</label></dt>
                                <dd><input type="text" class="small" id="datum2" value="<?php if ($row[datace_2] != 0) echo $row['datace_2']; ?>" name="datum2" maxlength="4" minlength="4" number="true"></dd>

                                <dt><label for="typ">Typ média *</label></dt>
                                <dd>
                                    <select id="typ" name="typ" size="1" class="select" required>
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_typmedia] ORDER BY %by', "typmedia", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($row[typmedia] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[typmedia]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[typmedia]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                                 

                                <dt><label for="skladovano">Archiv *</label></dt>
                                <dd>
                                    <select id="skladovano" name="skladovano" size="1" class="select" required>
                                        <?php
                                        $querylok = dibi::query('SELECT * FROM [pf_archiv] ORDER BY %by', "archiv", "ASC");
                                        while ($rowlok = $querylok->fetch()) {
                                            if ($row[skladovano] == $rowlok[id]) {
                                                echo "<option value=\"$rowlok[id]\" selected>$rowlok[archiv]</option>";
                                            } else {
                                                echo "<option value=\"$rowlok[id]\">$rowlok[archiv]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </dd>                               

                                <dt><label for="GPS">GPS</label></dt>
                                <dd><input type="text" class="" id="gps" name="gps" value="<?php echo $row[gps]; ?>" placeholder="GPS"></dd>                                  

                                <dt><label for="GPSor">GPS: marker nebo polygon?</label></dt>
                                <dd>
                                    <select id="gpsor" name="gpsor" size="1" class="select">
                                        <?php
                                        if ($row[gpsor] == "marker") {
                                            ?>
                                            <option value="marker" selected>marker</option>
                                            <option value="polygon">polygon</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="marker">marker</option>
                                            <option value="polygon" selected>polygon</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </dd>  

                                <dt><label for="klicovaslova">Klíčová slova *</label></dt>
                                <dd><textarea id="klicovaslova" name="klicovaslova" placeholder="Klíčová slova" required><?php echo $row[klicova_slova]; ?></textarea></dd>                                   

                                <dt><label for="popis">Popis</label></dt>
                                <dd><textarea id="popis" name="popis" placeholder="Popis"><?php echo $row[popis]; ?></textarea></dd>                                

                                <dt><label for="">Fotografie *</dt>
                                    <dd><?php get_foto_of_db_foto($_GET[ad]); ?></dd>           

                                    <dt><label for="">(*) označuje povinné položky</label></dt>
                                    <dd><input type="submit" class="button" id="submit1" value="Upravit fotografii"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "upravitkomentare") {
                    echo "<h1>Upravit komentáře: #" . get_idzaznamu_fotka($_GET[ad]) . "</h1>";
                    if ($_GET[smazat] != "") {

                        $arr = array('zverejnit' => 'ne');
                        dibi::query('UPDATE [pf_komentare] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář smazán!</h2><p></p></div>";
                    }
                    if ($_GET[kvalitni] != "") {

                        $arr = array('hodnoceni' => 'kvalitní');
                        dibi::query('UPDATE [pf_komentare] SET ', $arr, 'WHERE [id] = %i', $_GET[kvalitni]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář označen za kvalitní!</h2><p></p></div>";
                    }
                    if ($_GET[zavadejici] != "") {

                        $arr = array('hodnoceni' => 'zavádějící');
                        dibi::query('UPDATE [pf_komentare] SET ', $arr, 'WHERE [id] = %i', $_GET[zavadejici]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář označen zaz zavádějící!</h2><p></p></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_komentare] WHERE [id_fotka] = %i', $_GET[ad], 'AND [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        get_foto_of_user(get_id_uzivatele($row[uzivatel]));
                        echo "<a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row[uzivatel]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row[uzivatel]</a><br />";
                        echo echo_time($row[date]) . " | hodnocení: " . $row[hodnoceni] . " | (";


                        echo "<a href=\"formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&kvalitni=$row[id]\" onclick=\"if (confirm('Skutečně označit komentář jako kvalitní?'))
                                                                            location.href = './formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&kvalitni=$row[id];';
                                                                        return(false);\">kvalitní</a> / ";

                        echo "<a href=\"formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&zavadejici=$row[id]\" onclick=\"if (confirm('Skutečně označit komentář jako zavadejici?'))
                                                                            location.href = './formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&zavadejici=$row[id];';
                                                                        return(false);\">zavádějící</a> komentář) | ";

                        echo " <a href=\"formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat komentář?'))
                                                                            location.href = './formulare_admin.php?id=upravitkomentare&ad=$_GET[ad]&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a><br />";

                        echo nl2br($row[obsah]);

                        echo "</div><div class=\"clear\"></div>";
                    }
                } elseif ($_GET[id] == "upravitkomentareadmins") {
                    echo "<h1>Administrátorské komentáře: #" . get_idzaznamu_fotka($_GET[ad]) . "</h1>";
                    if ($_GET[action] == "send") {

                        $arr_akt = array('date' => time(), 'uzivatel' => $_SESSION['jmeno'], 'id_fotka' => $_GET['ad'], 'obsah' => $_POST['textarea1'], 'hodnoceni' => 'neověřený', 'zverejnit' => 'ano');
                        dibi::query('INSERT INTO [pf_komentare_admins]', $arr_akt);

                        //create_udalost("komentar", $_GET['ad'], get_idzaznamu_fotka($_GET['ad']));

                        send_msg_to_admins("<strong>$_SESSION[jmeno]</strong> přidal administrátorský komentář k fotografii: #" . get_idzaznamu_fotka($_GET[ad]));

                        echo "<div class=\"msg information\"><h2>Komentář byl přidán!</h2></div>";
                    }

                    if ($_GET[smazat] != "") {

                        $arr = array('zverejnit' => 'ne');
                        dibi::query('UPDATE [pf_komentare_admins] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář smazán!</h2><p></p></div>";
                    }
                    if ($_GET[kvalitni] != "") {

                        $arr = array('hodnoceni' => 'kvalitní');
                        dibi::query('UPDATE [pf_komentare_admins] SET ', $arr, 'WHERE [id] = %i', $_GET[kvalitni]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář označen za kvalitní!</h2><p></p></div>";
                    }
                    if ($_GET[zavadejici] != "") {

                        $arr = array('hodnoceni' => 'zavádějící');
                        dibi::query('UPDATE [pf_komentare_admins] SET ', $arr, 'WHERE [id] = %i', $_GET[zavadejici]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Komentář označen zaz zavádějící!</h2><p></p></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_komentare_admins] WHERE [id_fotka] = %i', $_GET[ad], 'AND [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        get_foto_of_user(get_id_uzivatele($row[uzivatel]));
                        echo "<a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row[uzivatel]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row[uzivatel]</a><br />";
                        echo echo_time($row[date]) . "<br />";


                        //echo "<a href=\"formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&kvalitni=$row[id]\" onclick=\"if (confirm('Skutečně označit komentář jako kvalitní?'))
                        //                                                  location.href = './formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&kvalitni=$row[id];';
                        //                                            return(false);\">kvalitní</a> / ";
                        //      echo "<a href=\"formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&zavadejici=$row[id]\" onclick=\"if (confirm('Skutečně označit komentář jako zavadejici?'))
                        //                                                        location.href = './formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&zavadejici=$row[id];';
                        //                                                  return(false);\">zavádějící</a> komentář) | ";
                        // echo " <a href=\"formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat komentář?'))
                        //                                                   location.href = './formulare_admin.php?id=upravitkomentareadmins&ad=$_GET[ad]&smazat=$row[id];';
                        //                                             return(false);\">&dagger; smazat</a><br />";

                        echo nl2br($row[obsah]);

                        echo "</div><div class=\"clear\"></div>";
                    }
                    ?>
                    <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                    <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravitkomentareadmins&ad=<?php echo $_GET[ad]; ?>&action=send">
                        <dl class="inline">
                            <dt><label for="textarea1">Text *</label></dt>
                            <dd><textarea id="textarea1" name="textarea1" placeholder="Text komentáře" required></textarea></dd>

                            <dt><label for="">(*) označuje povinné položky</label></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Přidat komentář"></dd>
                        </dl>
                    </form>
                    <?php
                } elseif ($_GET[id] == "upravitrelace") {
                    echo "<h1>Upravit relace: #" . get_idzaznamu_fotka($_GET[ad]) . "</h1>";
                    if ($_GET[smazat] != "") {

                        dibi::query('DELETE FROM [pf_databaze_relace] WHERE [id] = %i', $_GET[smazat]);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Relace smazána!</h2><p></p></div>";
                    }

                    if ($_GET[action] == "send") {

                        $arr_akt = array('id_jedna' => $_GET['ad'], 'id_dva' => $_POST['selectfotka'], 'popis' => $_POST['textarea1']);
                        dibi::query('INSERT INTO [pf_databaze_relace]', $arr_akt);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Relace byla přidána!</h2></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_databaze_relace] WHERE [id_jedna] = %i', $_GET[ad], 'OR [id_dva] = %i', $_GET[ad], ' ORDER BY %by', "id", "DESC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        echo "<strong>" . get_idzaznamu_fotka($row[id_jedna]) . " + " . get_idzaznamu_fotka($row[id_dva]) . "</strong> | ";
                        echo " <a href=\"formulare_admin.php?id=upravitrelace&ad=$_GET[ad]&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat komentář?'))
                                                                            location.href = './formulare_admin.php?id=upravitrelace&ad=$_GET[ad]&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a>";
                        echo "<br />" . nl2br($row[popis]);


                        echo "</div><div class=\"clear\"></div>";
                    }
                    ?>
                    <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                    <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravitrelace&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >
                        <dl class="inline">
                            <dt><label for="textarea1">Popis *</label></dt>
                            <dd><textarea id="textarea1" name="textarea1" placeholder="Text popisu relace" required></textarea></dd>

                            <div class="cara"> </div>     
                            <dt><label for="select">Navázat na fotku *</label></dt>
                            <dd>
                                <select id="selectfotka" name="selectfotka" size="5" class="select" required>
                                    <?php
                                    $query_restaurace = dibi::query("SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s", "ano", "AND [id] <> %i", $_GET[ad], "ORDER BY %by", "id", "ASC");
                                    while ($row_restaurace = $query_restaurace->fetch()) {

                                        $query_restaurace2 = dibi::query("SELECT * FROM [pf_databaze_relace] WHERE [id_jedna] = %i", $row_restaurace[id], 'AND [id_dva] = %i', $_GET[ad]);
                                        $row_restaurace2 = $query_restaurace2->count();
                                        $query_restaurace3 = dibi::query("SELECT * FROM [pf_databaze_relace] WHERE [id_jedna] = %i", $_GET[ad], 'AND [id_dva] = %i', $row_restaurace[id]);
                                        $row_restaurace3 = $query_restaurace3->count();

                                        if ($row_restaurace2 + $row_restaurace3 == 0)
                                            echo "<option value=\"$row_restaurace[id]\">$row_restaurace[id_zaznamu]</option>";
                                    }
                                    ?>
                                </select>                                   
                            </dd>                                
                            <dt><label for="">(*) označuje povinné položky</label></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Přidat relaci"></dd>
                        </dl>
                    </form>   
                    <?php
                }
                elseif ($_GET[id] == "databazelokalit") {
                    echo "<h1>Databáze lokalit</h1>";
                    if ($_GET[smazat] != "") {
                        dibi::query('DELETE FROM [pf_lokality] WHERE [id] = %i', $_GET[smazat]);

                        $arr_akt = array('lokalita' => 0,);
                        dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [lokalita] = %i', $_GET[smazat]);

                        echo "<div class=\"msg information\"><h2>Lokalita smazána!</h2><p></p></div>";
                    }

                    if ($_GET[upravit2] != "") {

                        $arr_akt = array('lokalita' => $_POST['lokalita']);
                        dibi::query('UPDATE [pf_lokality] SET', $arr_akt, 'WHERE [id] = %i', $_GET[upravit2]);

                        echo "<div class=\"msg information\"><h2>Lokalita upravena!</h2><p></p></div>";
                    }

                    if ($_GET[action] == "send") {
                        $arr_akt = array('lokalita' => $_POST['lokalita']);
                        dibi::query('INSERT INTO [pf_lokality]', $arr_akt);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Lokalita byla přidána!</h2></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_lokality] ORDER BY %by', "lokalita", "ASC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        echo "<strong>" . $row[lokalita] . " | ";
                        echo " <a href=\"formulare_admin.php?id=databazelokalit&upravit=$row[id]\">&harr; upravit</a>";
                        echo " <a href=\"formulare_admin.php?id=databazelokalit&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat lokalitu?'))
                                                                            location.href = './formulare_admin.php?id=databazelokalit&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a>";
                        echo "</div><div class=\"clear\"></div>";
                    }

                    if ($_GET[upravit]) {
                        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $_GET[upravit]);
                        $row = $query->fetch();
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazelokalit&upravit2=<?php echo $_GET[upravit]; ?>" >
                            <dl class="inline">
                                <dt><label for="lokalita">Lokalita *</label></dt>
                                <dd><input type="text" class="" id="lokalita" name="lokalita" value="<?php echo $row[lokalita]; ?>" placeholder="Lokalita" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Upravit lokalitu"></dd>
                            </dl>
                        </form>   
                        <?php
                    } else {
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazelokalit&action=send" >
                            <dl class="inline">
                                <dt><label for="lokalita">Lokalita *</label></dt>
                                <dd><input type="text" class="" id="lokalita" name="lokalita" value="<?php echo $row[lokalita]; ?>" placeholder="Lokalita" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat lokalitu"></dd>
                            </dl>
                        </form>   
                        <?php
                    }
                } elseif ($_GET[id] == "databazevydavatelu") {
                    echo "<h1>Databáze vydavatelů</h1>";
                    if ($_GET[smazat] != "") {
                        dibi::query('DELETE FROM [pf_vydavatel] WHERE [id] = %i', $_GET[smazat]);

                        $arr_akt = array('autorvydavatel' => 0,);
                        dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [autorvydavatel] = %i', $_GET[smazat]);

                        echo "<div class=\"msg information\"><h2>Vydavatel smazán!</h2><p></p></div>";
                    }

                    if ($_GET[upravit2] != "") {

                        $arr_akt = array('vydavatel' => $_POST['vydavatel']);
                        dibi::query('UPDATE [pf_vydavatel] SET', $arr_akt, 'WHERE [id] = %i', $_GET[upravit2]);

                        echo "<div class=\"msg information\"><h2>Vydavatel upraven!</h2><p></p></div>";
                    }

                    if ($_GET[action] == "send") {
                        $arr_akt = array('vydavatel' => $_POST['vydavatel']);
                        dibi::query('INSERT INTO [pf_vydavatel]', $arr_akt);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Vydavatel byl přidán!</h2></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_vydavatel] ORDER BY %by', "vydavatel", "ASC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        echo "<strong>" . $row[vydavatel] . " | ";
                        echo " <a href=\"formulare_admin.php?id=databazevydavatelu&upravit=$row[id]\">&harr; upravit</a>";
                        echo " <a href=\"formulare_admin.php?id=databazevydavatelu&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat vydavatele?'))
                                                                            location.href = './formulare_admin.php?id=databazevydavatelu&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a>";
                        echo "</div><div class=\"clear\"></div>";
                    }

                    if ($_GET[upravit]) {
                        $query = dibi::query('SELECT * FROM [pf_vydavatel] WHERE [id] = %i', $_GET[upravit]);
                        $row = $query->fetch();
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazevydavatelu&upravit2=<?php echo $_GET[upravit]; ?>" >
                            <dl class="inline">
                                <dt><label for="vydavatel">Vydavatel *</label></dt>
                                <dd><input type="text" class="" id="vydavatel" name="vydavatel" value="<?php echo $row[vydavatel]; ?>" placeholder="Vydavatel" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Upravit vydavatele"></dd>
                            </dl>
                        </form>   
                        <?php
                    } else {
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazevydavatelu&action=send" >
                            <dl class="inline">
                                <dt><label for="vydavatel">Vydavatel *</label></dt>
                                <dd><input type="text" class="" id="vydavatel" name="vydavatel" value="<?php echo $row[vydavatel]; ?>" placeholder="Vydavatel" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat vydavatele"></dd>
                            </dl>
                        </form>   
                        <?php
                    }
                } elseif ($_GET[id] == "databazetypumedia") {
                    echo "<h1>Databáze typů média</h1>";
                    if ($_GET[smazat] != "") {
                        dibi::query('DELETE FROM [pf_typmedia] WHERE [id] = %i', $_GET[smazat]);

                        $arr_akt = array('typmedia' => 0,);
                        dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [typmedia] = %i', $_GET[smazat]);

                        echo "<div class=\"msg information\"><h2>Typ média smazán!</h2><p></p></div>";
                    }

                    if ($_GET[upravit2] != "") {

                        $arr_akt = array('typmedia' => $_POST['typmedia']);
                        dibi::query('UPDATE [pf_typmedia] SET', $arr_akt, 'WHERE [id] = %i', $_GET[upravit2]);

                        echo "<div class=\"msg information\"><h2>Typ média upraven!</h2><p></p></div>";
                    }

                    if ($_GET[action] == "send") {
                        $arr_akt = array('typmedia' => $_POST['typmedia']);
                        dibi::query('INSERT INTO [pf_typmedia]', $arr_akt);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Typ média byl přidán!</h2></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_typmedia] ORDER BY %by', "typmedia", "ASC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        echo "<strong>" . $row[typmedia] . " | ";
                        echo " <a href=\"formulare_admin.php?id=databazetypumedia&upravit=$row[id]\">&harr; upravit</a>";
                        echo " <a href=\"formulare_admin.php?id=databazetypumedia&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat typ média?'))
                                                                            location.href = './formulare_admin.php?id=databazetypumedia&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a>";
                        echo "</div><div class=\"clear\"></div>";
                    }

                    if ($_GET[upravit]) {
                        $query = dibi::query('SELECT * FROM [pf_typmedia] WHERE [id] = %i', $_GET[upravit]);
                        $row = $query->fetch();
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazetypumedia&upravit2=<?php echo $_GET[upravit]; ?>" >
                            <dl class="inline">
                                <dt><label for="typmedia">Typ média *</label></dt>
                                <dd><input type="text" class="" id="typmedia" name="typmedia" value="<?php echo $row[typmedia]; ?>" placeholder="Typ média" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Upravit typ média"></dd>
                            </dl>
                        </form>   
                        <?php
                    } else {
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazetypumedia&action=send" >
                            <dl class="inline">
                                <dt><label for="typmedia">Typ média *</label></dt>
                                <dd><input type="text" class="" id="typmedia" name="typmedia" value="<?php echo $row[typmedia]; ?>" placeholder="Typ média" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat typ média"></dd>
                            </dl>
                        </form>   
                        <?php
                    }
                } elseif ($_GET[id] == "databazearchivu") {
                    echo "<h1>Databáze archivů</h1>";
                    if ($_GET[smazat] != "") {
                        dibi::query('DELETE FROM [pf_archiv] WHERE [id] = %i', $_GET[smazat]);

                        $arr_akt = array('skladovano' => 0,);
                        dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [skladovano] = %i', $_GET[smazat]);

                        echo "<div class=\"msg information\"><h2>Archiv smazán!</h2><p></p></div>";
                    }

                    if ($_GET[upravit2] != "") {

                        $arr_akt = array('archiv' => $_POST['archiv']);
                        dibi::query('UPDATE [pf_archiv] SET', $arr_akt, 'WHERE [id] = %i', $_GET[upravit2]);

                        echo "<div class=\"msg information\"><h2>Archiv upraven!</h2><p></p></div>";
                    }

                    if ($_GET[action] == "send") {
                        $arr_akt = array('archiv' => $_POST['archiv']);
                        dibi::query('INSERT INTO [pf_archiv]', $arr_akt);

                        foto_was_edited($_GET[ad]);

                        echo "<div class=\"msg information\"><h2>Archiv byl přidán!</h2></div>";
                    }


                    $query = dibi::query('SELECT * FROM [pf_archiv] ORDER BY %by', "archiv", "ASC");
                    while ($row = $query->fetch()) {
                        echo "<div class=\"block\">";
                        echo "<strong>" . $row[archiv] . " | ";
                        echo " <a href=\"formulare_admin.php?id=databazearchivu&upravit=$row[id]\">&harr; upravit</a>";
                        echo " <a href=\"formulare_admin.php?id=databazearchivu&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat archiv?'))
                                                                            location.href = './formulare_admin.php?id=databazearchivu&smazat=$row[id];';
                                                                        return(false);\">&dagger; smazat</a>";
                        echo "</div><div class=\"clear\"></div>";
                    }

                    if ($_GET[upravit]) {
                        $query = dibi::query('SELECT * FROM [pf_archiv] WHERE [id] = %i', $_GET[upravit]);
                        $row = $query->fetch();
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazearchivu&upravit2=<?php echo $_GET[upravit]; ?>" >
                            <dl class="inline">
                                <dt><label for="archiv">Archiv *</label></dt>
                                <dd><input type="text" class="" id="archiv" name="archiv" value="<?php echo $row[archiv]; ?>" placeholder="Archiv" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Upravit archiv"></dd>
                            </dl>
                        </form>   
                        <?php
                    } else {
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=databazearchivu&action=send" >
                            <dl class="inline">
                                <dt><label for="archiv">Archiv *</label></dt>
                                <dd><input type="text" class="" id="archiv" name="archiv" value="<?php echo $row[archiv]; ?>" placeholder="Archiv" required></dd> 

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat archiv"></dd>
                            </dl>
                        </form>   
                        <?php
                    }
                }
            } else {
                ?>
                <div class="msg err"><h2>Nemáš tu co dělat! Nejsi admin!</h2><p>Jdi na stránky <a href="./index.php">FOTOPACOVA</a>!</p></div>
                <?php
            }
            ?>
        </div>
    </body>
</html>        


