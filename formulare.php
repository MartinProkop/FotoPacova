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
        <?php
        // jazyk
        if ($_GET[lang] == null) {
            $_GET[lang] = "cs";
        }
        ?>

        <title>FOTOPACOV</title>

        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>

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


    </head>
    <body>
        <div id="okno_form">
            <?php if (check_mobile()) echo "<h2><a href=\"JavaScript:history.back()\" class=\"button_zpet\">Vrátit se zpět</a></h2>"; ?>

            <?php
            if ($_GET[id] == "registrace") {
                echo "<h1>Registrace</h1>";
                $no_error = true;

                if ($_GET[action] == "send") {
                    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_POST[jmeno], "AND stav <> %s", "smazan");
                    $query2 = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [email] = %s', $_POST[email], "AND stav <> %s", "smazan");

                    if ($query->count() > 0) {
                        echo "<div class=\"msg err\"><h2>Gurmán s tímto jménem již existuje!</h2><p>Vyberte jiné jméno.</p></div>";
                        $no_error = false;
                    }

                    if ($query2->count() > 0) {
                        echo "<div class=\"msg err\"><h2>Gurmán s tímto emailem již existuje!</h2><p>Vyberte jiný email.</p></div>";
                        $no_error = false;
                    }

                    if ($_POST[passwordinput] != $_POST[passwordinput2]) {
                        echo "<div class=\"msg err\"><h2>Vámi zadaná hesla se neshodují!</h2><p>Opakujte zadání hesla.</p></div>";
                        $no_error = false;
                    }

                    if ($_FILES['fileToUpload']['name'] != "") {
                        if ($_FILES["fileToUpload"]["size"] > 1000000) {
                            echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                            $no_error = false;
                        } else {
                            $timestamp = time();
                            $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'];
                            $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "JPEG" && $imageFileType != "gif") {
                                echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                $no_error = false;
                            } else {
                                move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");
                                $upload = "ok";
                            }
                        }
                    }

                    //uložime
                    if ($no_error) {
                        // uživatel
                        $heslo_hash = md5($_POST[passwordinput]);

                        $arr_uzivatel = array('jmeno' => $_POST['jmeno'], 'email' => $_POST['email'], 'pohlavi' => $_POST['radio1'], 'popis' => $_POST['textarea1'], 'date' => time(), 'heslo_hash' => $heslo_hash);
                        dibi::query('INSERT INTO [pf_uzivatele]', $arr_uzivatel);

                        $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_POST[jmeno]);
                        while ($row = $query->fetch()) {
                            $id_uzivatel = $row[id];
                        }
                        
                        //create_udalost_user($_POST['jmeno']);

                        // foto
                        if ($upload == "ok") {
                            $newname = "./obrazky/" . $nazev_souboru;
                            $oldname = $cil;
                            rename($oldname, $newname);
                            $arr_fotka = array('nazev' => $_POST['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'popis' => $_POST['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pf_fotky]', $arr_fotka);
                        } elseif ($_POST[uploaded_picture] != "") {
                            $newname = "./obrazky/" . $_POST[uploaded_picture_nazev];
                            $oldname = $_POST[uploaded_picture];
                            rename($oldname, $newname);
                            $arr_fotka = array('nazev' => $_POST['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'popis' => $_POST['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pf_fotky]', $arr_fotka);
                        }
                        //mail
                        //    $body = "Dobrý den,\n\nděkujeme za Vaši registraci na serveru Pijem, jíme, hodnotíme.\n\nVaše uživatelské jméno: " . $_POST['jmeno'] . "\nVaše heslo je: " . $_POST[passwordinput] . "\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        //  send_mail_kovar($_POST['email'], "Registrace na serveru pjhvysocina.cz", $body);

                        echo "<div class=\"msg information\"><h2>Jste úspěšně registrován jako '" . $_POST[jmeno] . "'!</h2><p>Na email jsme Vám odeslali potvrzení registrace.<br />Zavřete okno nebo <a href=\"./formulare.php?id=prihlaseni&ad=" . $_POST[jmeno] . "\">se přihlašte</a>.</p></div>";
                    }
                }

                if ($_GET[action] != "send" || $no_error == false) {
                    ?>
                    <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=registrace&action=send" enctype="multipart/form-data">
                        <dl class="inline">

                            <dt><label for="jmeno">Uživatelské jméno *</label></dt>
                            <dd><input type="text" class="" id="jmeno" name="jmeno" placeholder="Vaše uživatelské jméno" value="<?php echo $_POST[jmeno]; ?>" required></dd>
                            <dt><label for="email">Email *</label></dt>
                            <dd><input type="text" class="" id="email" name="email" placeholder="Váš email" value="<?php echo $_POST[email]; ?>" required email="true"></dd>  

                            <?php
                            if ($_POST[radio1] == "Muž") {
                                $active[muz] = "checked";
                            } elseif ($_POST[radio1] == "Žena") {
                                $active[zena] = "checked";
                            }
                            ?>

                            <dt><label for="radio-1">Pohlaví *</label></dt>
                            <dd>
                                <input name="radio1" type="radio" id="radio-1" value="Muž" <?php echo $active[muz]; ?> required><label for="radio-1">Muž</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="radio1" type="radio" id="radio-2" value="Žena" <?php echo $active[zena]; ?> ><label for="radio-2">Žena</label>
                            </dd>                

                            <dt><label for="fileToUpload">Profilové foto</label></dt>
                            <dd>
                                <?php
                                if ($upload == "ok") {
                                    echo "<img src=\"" . $cil . "\" height=\"100px\"/>
                                      <input type=\"hidden\" name=\"uploaded_picture\" value=\"" . $cil . "\">
                                      <input type=\"hidden\" name=\"uploaded_picture_nazev\" value=\"" . $nazev_souboru . "\">";
                                } elseif ($_POST[uploaded_picture] != "") {
                                    echo "<img src=\"" . $_POST[uploaded_picture] . "\" height=\"100px\"/>
                                      <input type=\"hidden\" name=\"uploaded_picture\" value=\"" . $_POST[uploaded_picture] . "\">
                                      <input type=\"hidden\" name=\"uploaded_picture_nazev\" value=\"" . $_POST[uploaded_picture_nazev] . "\">";
                                } else {
                                    echo "<input type = \"file\" class=\"big\" name=\"fileToUpload\" id=\"fileToUpload\">";
                                }
                                ?>
                            </dd> 

                            <dt><label for="textarea1">Povězte něco o sobě *</label></dt>
                            <dd><textarea id="textarea1" name="textarea1" required maxlength="500" placeholder="Povězte něco o sobe (do 500 znaků)"><?php echo $_POST[textarea1]; ?></textarea></dd>

                            <dt><label for="passwordinput">Heslo *</label></dt>
                            <dd><input type="password" class="" placeholder="Vaše heslo" id="passwordinput" name="passwordinput" required></dd>

                            <dt><label for="passwordinput2">Zopakujte heslo *</label></dt>
                            <dd><input type="password" class="" placeholder="Zopakujte Vaše heslo" id="passwordinput2" name="passwordinput2" required></dd>

                            <dt><label for="">(*) označuje povinné položky</label></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Registrovat"></dd>
                        </dl>
                    </form>
                    <?php
                }
            } if ($_GET[id] == "nastaveniuzivatele") {
                ?>            
                <h1>Nastavení uživatele</h1>
                <?php
                $no_error_heslo = true;
                $no_error_foto = true;
                $no_error_popis = true;

                if ($_GET[smazatfoto] == "ano") {
                    $query_maze_foto = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', get_id_uzivatele($_SESSION[jmeno]), 'AND [typ] = %s', 'profilova');
                    $row_maze_foto = $query_maze_foto->fetch();
                    unlink($row_maze_foto[cil]);
                    dibi::query('DELETE FROM [pf_fotky] WHERE [id] = %i', $row_maze_foto[id]);
                }


                if ($_GET[action] == "send") {
                    if ($_POST[passwordinput] != $_POST[passwordinput2]) {
                        echo "<div class=\"msg err\"><h2>Vámi zadaná nová hesla se neshodují!</h2><p>Opakujte zadání hesla.</p></div>";
                        $no_error_heslo = false;
                    }

                    if ($_FILES['fileToUpload']['name'] != "") {
                        if ($_FILES["fileToUpload"]["size"] > 1000000) {
                            echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                            $no_error_foto = false;
                        } else {
                            $timestamp = time();
                            $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'];
                            $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                $no_error_foto = false;
                            } else {
                                move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");
                                $upload = "ok";
                            }
                        }
                    } else {
                        $no_error_foto = false;
                    }

                    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno]);
                    while ($row = $query->fetch()) {
                        $id_uzivatel = $row[id];
                        $email = $row[email];
                        $pohlavi = $row[pohlavi];
                        $popis = $row[popis];
                    }

                    //uložime
                    if ($no_error_heslo) {
                        if ($_POST[passwordinput] != "") {
                            $heslo_hash = md5($_POST[passwordinput]);

                            $_SESSION[heslo] = $heslo_hash;

                            $arr = array('heslo_hash' => $heslo_hash);
                            dibi::query('UPDATE [pf_uzivatele] SET ', $arr, 'WHERE [id] = %i', $id_uzivatel);

                            echo "<div class=\"msg information\"><h2>Změna Vašeho hesla proběhla úspěšně!</h2></div>";
                        }
                    }

                    if ($no_error_popis) {
                        // uživatel
                        $arr = array('popis' => $_POST['textarea1']);
                        dibi::query('UPDATE [pf_uzivatele] SET ', $arr, 'WHERE [id] = %i', $id_uzivatel);
                    }

                    if ($no_error_foto) {
                        // foto
                        if ($upload == "ok") {
                            $query2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id_uzivatel, 'AND [typ] = %s', 'profilova');
                            while ($row2 = $query2->fetch()) {
                                $fotka = $row2[cil];
                                $fotkaid = $row2[id];
                            }
                            unlink($fotka);
                            dibi::query('DELETE FROM [pf_fotky] WHERE [id] = %i', $fotkaid);

                            $newname = "./obrazky/" . $nazev_souboru;
                            $oldname = $cil;
                            rename($oldname, $newname);

                            $arr_fotka = array('nazev' => $_SESSION['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'popis' => $_SESSION['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pf_fotky]', $arr_fotka);
                        } elseif ($_POST[uploaded_picture] != "") {
                            $query2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id_uzivatel, 'AND [typ] = %s', 'profilova');
                            while ($row2 = $query2->fetch()) {
                                $fotka = $row2[cil];
                                $fotkaid = $row2[id];
                            }
                            unlink($fotka);
                            dibi::query('DELETE FROM [pf_fotky] WHERE [id] = %i', $fotkaid);

                            $newname = "./obrazky/" . $_POST[uploaded_picture_nazev];
                            $oldname = $_POST[uploaded_picture];
                            rename($oldname, $newname);

                            $arr_fotka = array('nazev' => $_SESSION['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'popis' => $_SESSION['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pf_fotky]', $arr_fotka);
                        }

                        echo "<div class=\"msg information\"><h2>Změna fotografie proběhla úspěšně!</h2></div>";
                    }
                }


                $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno]);
                while ($row = $query->fetch()) {
                    $id_uzivatele = $row[id];
                    $email = $row[email];
                    $pohlavi = $row[pohlavi];
                    $popis = $row[popis];
                }

                $query2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id_uzivatele, 'AND [typ] = %s', 'profilova');
                if ($query2->count() == 1) {
                    $row2 = $query2->fetch();
                    $fotka = "<img src=\"$row2[cil]\" height=\"100px\"/>";
                } else {
                    $fotka = "<label for=\"\">Prozatím nemá fotku</label>";
                }
                ?>       
                <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                <form id="formul" name="formul" method="post" action="./formulare.php?id=nastaveniuzivatele&action=send" enctype="multipart/form-data">
                    <dl class="inline">
                        <dt><label for="jmeno">Uživatelské jméno *</label></dt>
                        <dd><input type="text" class="" id="jmeno" name="jmeno" placeholder="Vaše uživatelské jméno" value="<?php echo $_SESSION[jmeno]; ?>" required disabled></dd>
                        <dt><label for="email">Email *</label></dt>
                        <dd><input type="text" class="" id="email" name="email" placeholder="Váš email" value="<?php echo $email; ?>" required email="true" disabled></dd>  

                        <?php
                        if ($pohlavi == "Muž") {
                            $active[muz] = "checked";
                        } elseif ($pohlavi == "Žena") {
                            $active[zena] = "checked";
                        }
                        ?>

                        <dt><label for="radio-1">Pohlaví *</label></dt>
                        <dd>
                            <input name="radio1" type="radio" id="radio-1" value="Muž" <?php echo $active[muz]; ?> required disabled><label for="radio-1">Muž</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="radio1" type="radio" id="radio-2" value="Žena" <?php echo $active[zena]; ?> disabled><label for="radio-2">Žena</label>
                        </dd>     

                        <dt><label for="">Profilové foto</label><?php if ($query2->count() == 1) echo "<br /><a href=\"./formulare.php?id=nastaveniuzivatele&smazatfoto=ano\">Smazat tuto fotografii</a>"; ?></dt>
                        <dd><?php echo $fotka; ?></dd> 

                        <dt><label for="fileToUpload">Nové profilové foto</label></dt>
                        <dd><input type="file" class="" name="fileToUpload" id="fileToUpload"></dd>                             

                        <dt><label for="textarea1">Povězte něco o sobě *</label></dt>
                        <dd><textarea id="textarea1" name="textarea1" required maxlength="500" placeholder="Povězte něco o sobě (do 500 znaků)"><?php echo $popis; ?></textarea></dd>

                        <dt><label for="passwordinput">Nové heslo</label></dt>
                        <dd><input type="password" class="" placeholder="Vaše nové heslo" id="passwordinput" name="passwordinput"></dd>

                        <dt><label for="passwordinput2">Zopakujte nové heslo</label></dt>
                        <dd><input type="password" class="" placeholder="Zopakujte Vaše nové heslo" id="passwordinput2" name="passwordinput2"></dd>

                        <dt><label for="">(*) označuje povinné položky</label></dt>
                        <dd><input type="submit" class="button" id="submit1" value="Upravit"></dd>
                    </dl>
                </form>
                <?php
            } elseif ($_GET[id] == "prihlaseni") {
                ?>         
                <h1>Přihlášení</h1>
                <?php
                if ($_POST['pokusolog'] == "1" && loguj($_SESSION['jmeno'], $_SESSION['heslo'], $_POST[zustatprihlasen]) == 1) {
                    echo "<div class=\"msg information\"><h2>Přihlášeno!</h2></div>";
                    ?>
                    <script>
                        close_fancybox_redirect_parent('./index.php', 3000);
                    </script>
                    <?php
                } elseif (loguj($_SESSION['jmeno'], $_SESSION['heslo'], $_POST[zustatprihlasen]) == 2 && $_POST['pokusolog'] == "1") {
                    echo "<div class=\"msg err\"><h2>Špatně zadané jméno nebo heslo!</h2></div>";
                    ?>
                    <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=prihlaseni" >
                        <input type="hidden" name="pokusolog" value="1">
                        <input type="hidden" name="redirect" value="<?php echo $_GET[redirect]; ?>">
                        <dl class="inline">
                            <dt><label for="jmeno">Uživ. jméno / e-mail</label></dt>
                            <dd><input type="text" class="" id="jmeno" name="jmeno" value="<?php echo $_POST[jmeno]; ?>" placeholder="Vaše uživatelské jméno nebo e-mail" required></dd>
                            <dt><label for="heslo">Heslo</label></dt>
                            <dd><input type="password" class="" id="heslo" name="heslo" placeholder="Vaše heslo" required></dd>
                            <dt><label for="zustatprihlasen">Zůstat přihlášen?</label></dt>
                            <dd><input name="zustatprihlasen" type="checkbox" value="ano" id="zustatprihlasen" name="zustatprihlasen"><label for="zustatprihlasen">Ano</label></dd>
                            <dt><a href="./formulare.php?id=zapomenuteheslo">Zapoměli jste jméno nebo heslo?</a></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Přihlásit">
                        </dl>
                    </form>                
                    <?php
                } else {
                    ?>
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=prihlaseni" >
                        <input type="hidden" name="pokusolog" value="1">
                        <input type="hidden" name="redirect" value="<?php echo $_GET[redirect]; ?>">
                        <dl class="inline">
                            <dt><label for="jmeno">Uživ. jméno / e-mail</label></dt>
                            <dd><input type="text" class="" id="jmeno" name="jmeno" value="<?php echo $_GET[ad]; ?>" placeholder="Vaše uživatelské jméno nebo e-mail" required></dd>
                            <dt><label for="heslo">Heslo</label></dt>
                            <dd><input type="password" class="" id="heslo" name="heslo" placeholder="Vaše heslo" required></dd>
                            <dt><label for="zustatprihlasen">Zůstat přihlášen?</label></dt>
                            <dd><input name="zustatprihlasen" type="checkbox" value="ano" id="zustatprihlasen" name="zustatprihlasen"><label for="zustatprihlasen">Ano</label></dd>
                            <dt><a href="./formulare.php?id=zapomenuteheslo">Zapoměli jste jméno nebo heslo?</a></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Přihlásit">
                        </dl>
                    </form>
                    <?php
                }
            } elseif ($_GET[id] == "zapomenuteheslo") {
                ?>      
                <h1>Zapomenté jméno nebo heslo</h1>
                <?php
                if ($_POST['pokusolog'] == "1") {

                    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [email] = %s', $_POST[textinput1]);
                    if ($query->count() == 1) {
                        $row = $query->fetch();
                        echo "<div class=\"msg information\"><h2>Odeslali jsme Vám email s Vaším novým heslem!</h2><p>V emailu jsme Vám zaslali nové heslo.</p></div>";

                        $nove_heslo = generateRandomString(10);
                        $hash_hesla = md5($nove_heslo);

                        echo $nove_heslo;

                        $arr = array('heslo_hash' => $hash_hesla);
                        dibi::query('UPDATE [pf_uzivatele] SET ', $arr, 'WHERE [id] = %i', $row[id]);

                        //      $body = "Dobrý den,\n\nna základě Vaší žádosti jsme Vám vygenerovali nové heslo na serveru Pijem, jíme, hodnotíme.\n\nVaše uživatelské jméno: " . $row['jmeno'] . "\nVaše heslo je: " . $nove_heslo . "\n\nHeslo můžete kdykoliv změnit v sekci nastavení.\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        //    send_mail_kovar($_POST[textinput1], "Zapomenuté heslo na serveru pjhvysocina.cz", $body);
                    } else {
                        echo "<div class=\"msg err\"><h2>Uživatel nenalezen - neznámý email!</h2><p>Pod takovým emailem není registrován žádný uživatel. Zkuste to znovu.</p></div>";
                        ?>
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" >
                            <input type="hidden" name="pokusolog" value="1">
                            <dl class="inline">
                                <dt><label for="textinput1">Váš e-mail</label></dt>
                                <dd><input type="text" class="" id="textinput1" name="textinput1" placeholder="Váš e-mail" email="true" required></dd>
                                <dt></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Odeslat"></dd>
                            </dl>
                        </form>  
                        <?php
                    }
                } else {
                    ?>
                    <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                    <form id="formul" name="formul" method="post" >
                        <input type="hidden" name="pokusolog" value="1">
                        <dl class="inline">
                            <dt><label for="textinput1">Váš e-mail</label></dt>
                            <dd><input type="text" class="" id="textinput1" name="textinput1" placeholder="Váš e-mail" email="true" required></dd>
                            <dt></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Odeslat"></dd>
                        </dl>
                    </form>  
                    <?php
                }
            }


            //asi nic
            elseif ($_GET[id] == "uzivatel") {
                $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();


                get_foto_of_user($_GET[ad]);
                echo "<h1>".$row[jmeno]."</h1><br />";
                echo "<p>".$row[popis]."</p>";
             
            } elseif ($_GET[id] == "zpravy") {
                echo "<h1>Zprávy</h1>";
                $query = dibi::query('SELECT * FROM [pf_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'ORDER BY %by', 'date', 'DESC');

                $query2 = dibi::query('SELECT * FROM [pf_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'AND [stav] = %s', 'nova');
                if ($query2->count() == 0)
                    echo "<div class=\"msg information\"><h2>Nemáte žádnou novou zprávu!</h2></div>";

                $i = 1;
                while ($row = $query->fetch()) {
                    $arr = array('stav' => '');
                    dibi::query('UPDATE [pf_zpravy] SET ', $arr, 'WHERE [id] = %i', $row[id]);
                    ?>
                    <div class="block">
                    <?php echo $i; ?>. 
                        <?php
                        if ($row[stav] == "nova")
                            echo "nová ";
                        else
                            echo "přečtená "
                            ?>(<?php echo echo_time($row[date]); ?>): 
                        <?php echo nl2br($row[obsah]); ?>

                    </div>
                    <div class="clear"></div>
                    <?php
                    $i++;
                }
            } elseif ($_GET[id] == "novykomentar") {
                echo "<h1>Nový komentář</h1>";
                if (login_check()) {
                    if ($_GET[action] == "send") {

                        $arr_akt = array('date' => time(), 'uzivatel' => $_SESSION['jmeno'], 'id_fotka' => $_GET['ad'], 'obsah' => $_POST['textarea1'], 'hodnoceni' => 'neověřený', 'zverejnit' => 'ano');
                        dibi::query('INSERT INTO [pf_komentare]', $arr_akt);

                        create_udalost("komentar", $_GET['ad'], get_idzaznamu_fotka($_GET['ad']));

                        echo "<div class=\"msg information\"><h2>Komentář byl přidán!</h2></div>";
                        ?>
                        <script>

                            close_fancybox_redirect_parent('./index.php?id=vyhledavani&ad=<?php echo $_GET[ad] ?>', 3000);
                        </script>
                        <?php
                    }

                    if ($_GET[action] != "send") {
                        ?>       
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare.php?id=novykomentar&ad=<?php echo $_GET[ad]; ?>&action=send">
                            <dl class="inline">
                                <dt><label for="textarea1">Text *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Text komentáře" required><?php echo $_POST[textarea1]; ?></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat komentář"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } else {
                    echo "<div class=\"msg err\"><h2>Přidávat komentáře může jen registrovaný uživatel!</h2><p>Můžete se <a href=\"./formulare.php?id=prihlaseni\">přihlásit</a> nebo <a href=\"./formulare.php?id=registrace\">registrovat</a>.</p></div>";
                }
            }
            ?>
        </div>
    </body>
</html>        


