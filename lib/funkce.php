<?php

function format_size($cesta, $round = 3) {
    //Size must be bytes!
    $size = filesize($cesta);
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    for ($i = 0; $size > 1024 && $i < count($sizes) - 1; $i++)
        $size /= 1024;
    return round($size, $round) . " " . $sizes[$i];
}

function echo_date($timestamp) {
    return Date("d. m. Y", $timestamp);
}

function echo_time($timestamp) {
    return Date("H:i:s d. m. Y", $timestamp);
}

function echo_date_picker($timestamp) {

    echo Date("m", $timestamp) . "/" . Date("d", $timestamp) . "/" . Date("m", $timestamp);
}

function number_of_comments($id) {
    $query = dibi::query('SELECT * FROM [pf_komentare] WHERE [id_fotka] = %i', $id, 'AND [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
    return $query->count();
}

function number_of_comments_admins($id) {
    $query = dibi::query('SELECT * FROM [pf_komentare_admins] WHERE [id_fotka] = %i', $id, 'AND [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
    return $query->count();
}

function number_of_relations($id) {
    $query = dibi::query('SELECT * FROM [pf_databaze_relace] WHERE [id_jedna] = %i', $id, 'OR [id_dva] = %i', $id, ' ORDER BY %by', "id", "DESC");
    return $query->count();
}

function echo_relations($id) {
    $query = dibi::query('SELECT * FROM [pf_databaze_relace] WHERE [id_jedna] = %i', $id, 'OR [id_dva] = %i', $id, ' ORDER BY %by', "id", "DESC");
    if ($query->count() > 0) {
        echo "relace na: ";
        while ($row = $query->fetch()) {
            if ($row[id_jedna] == $id)
                echo "<a href=\"./index.php?id=vyhledavani&ad=$row[id_dva]\">" . get_idzaznamu_fotka($row[id_dva]) . "</a> ($row[popis]), ";
            if ($row[id_dva] == $id)
                echo "<a href=\"./index.php?id=vyhledavani&ad=$row[id_jedna]\">" . get_idzaznamu_fotka($row[id_jedna]) . "</a> ($row[popis]), ";
        }
    }
}

function echo_lokalita_of_foto_db($lokalita) {
    $pole = explode(",", $lokalita);
    $retez = "";

    if ($pole[0] != "0") {
        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole[0]);
        $row = $query->fetch();
        $retez = $retez . $row[lokalita];
    }
    if ($pole[1] != "0") {
        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole[1]);
        $row = $query->fetch();
        $retez = $retez . ", " . $row[lokalita];
    }
    if ($pole[2] != "0") {
        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole[2]);
        $row = $query->fetch();
        $retez = $retez . ", " . $row[lokalita];
    }
    if ($pole[3] != "0") {
        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole[3]);
        $row = $query->fetch();
        $retez = $retez . ", " . $row[lokalita];
    }
    if ($pole[4] != "0") {
        $query = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole[4]);
        $row = $query->fetch();
        $retez = $retez . ", " . $row[lokalita];
    }
    return $retez;
}

function echo_vydavatel_of_foto_db($vydavatel) {

    if ($vydavatel != "0") {
        $query = dibi::query('SELECT * FROM [pf_vydavatel] WHERE [id] = %i', $vydavatel);
        $row = $query->fetch();
        return $row[vydavatel];
    }

    return "není znám";
}

function echo_typmedia_of_foto_db($typmedia) {

    if ($typmedia != "0") {
        $query = dibi::query('SELECT * FROM [pf_typmedia] WHERE [id] = %i', $typmedia);
        $row = $query->fetch();
        return $row[typmedia];
    }

    return "není známo";
}

function echo_archiv_of_foto_db($archiv) {

    if ($archiv != "0") {
        $query = dibi::query('SELECT * FROM [pf_archiv] WHERE [id] = %i', $archiv);
        $row = $query->fetch();
        return $row[archiv];
    }

    return "není známo";
}

function echo_datace_of_foto_db($id) {

    $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $id);
    $row = $query->fetch();

    if ($row[datace_1] == 0) {
        return "datace není známá";
    } else {
        if ($row[datace_2] == 0) {
            return $row[datace_1];
        } else {
            return $row[datace_1] . " - " . $row[datace_2];
        }
    }
}

function number_of_db_foto() {
    $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s', "ano");
    echo $query->count();
}

function generateRandomString($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function get_foto_of_user($id) {
    $query = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id, 'AND [typ] = %s', 'profilova');
    $row = $query->fetch();

    if ($row[cil] == "")
        return false;
    else
        echo "<img class=\"imgLeftSmall\" src=\"$row[cil]\" alt=\"$row[nazev]\"/>";
}

function get_foto_of_clanek($id) {
    $query = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id, 'AND [typ] = %s', 'clanek', 'AND [zverejnit] = %s', 'ano', 'ORDER BY %by', 'poradi', "ASC");
    while ($row = $query->fetch()) {
        ?>
        <a href="<?php echo $row[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row[nazev]; ?>" title="<?php echo $row[nazev]; ?>"><img class="imgLeft" src="<?php echo $row[cil]; ?>" alt="<?php echo $row[nazev]; ?>"/></a>
        <?php
    }
}

function get_foto_of_clanek_title($id) {
    $query = dibi::query('SELECT * FROM [pf_fotky] WHERE [id_cil] = %i', $id, 'AND [typ] = %s', 'clanek', 'AND [zverejnit] = %s', 'ano', 'ORDER BY %by', 'poradi', "ASC");
    $row = $query->fetch();
    ?>
    <a href="<?php echo $row[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row[nazev]; ?>" title="<?php echo $row[nazev]; ?>"><img class="imgLeft" src="<?php echo $row[cil]; ?>" alt="<?php echo $row[nazev]; ?>"/></a>
    <?php
}

function get_foto_of_db_foto($id, $big = "small") {
    $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $id);
    while ($row = $query->fetch()) {
        ?>
        <a href="<?php echo $row[cil]; ?>"  rel="fp_gal" class="fancybox-buttons" title="#<?php
        echo $row[id_zaznamu] . ": " . echo_lokalita_of_foto_db($row[lokalita]) . ", " . echo_datace_of_foto_db($id) . ". ";
        echo $row[popis] . " <a href='./index.php?id=vyhledavani&ad=$id'>[DETAIL FOTOGRAFIE]</a>";
        ?>"><img class="imgLeft<?php if ($big == "big") echo "Big"; ?>" src="<?php echo $row[cil]; ?>" alt="<?php echo $row[nazev]; ?>"/></a>
        <?php
    }
}

function get_parse_string_of_db_foto($retez) {
    $pole = explode(";", $retez);
    $r = "";
    for ($i = 0; $i < count($pole) - 1; $i++) {
        if ($i == O) {
            $r = $pole[$i];
        } else {
            $r = $r . ", " . $pole[$i];
        }
    }

    return $r;
}

function get_id_uzivatele($jmeno) {
    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $jmeno);
    $row = $query->fetch();

    return $row[id];
}

function get_email_uzivatele($id) {
    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [id] = %i', $id);
    $row = $query->fetch();

    return $row[email];
}

function get_idzaznamu_fotka($id) {
    $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $id);
    $row = $query->fetch();

    return $row[id_zaznamu];
}

function foto_was_edited($id) {
    $arr_akt = array('date_uprava' => time(), 'zadavatel_admin_uprava' => $_SESSION['jmeno']);
    dibi::query('UPDATE [pf_databaze] SET', $arr_akt, 'WHERE [id] = %i', $id);
}

function send_msg_to_admins($zprava) {
    $query_chci_id = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [admin] = %s', "ano");
    while ($row_chci_id = $query_chci_id->fetch()) {
        $arr_temp = array('id_cil' => $row_chci_id[id], 'date' => time(), 'obsah' => $zprava, 'stav' => "nova");
        dibi::query('INSERT INTO [pf_zpravy]', $arr_temp);
    }
}

function echo_pocet_novych_zprav() {
    $query = dibi::query('SELECT * FROM [pf_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'AND [stav] = %s', 'nova');
    if ($query->count() > 0)
        return "<span class=\"varovani\">" . $query->count() . "</span>";
}

function send_mail_kovar($to, $subject, $body) {
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'mail.ldekonom.cz';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'info@pjhvysocina.cz';                 // SMTP username
    $mail->Password = 'kH28ndu2BZDb2GGB';                           // SMTP password
    $mail->SMTPSecure = '';                            // Enable encryption, 'ssl' also accepted

    $mail->From = "info@pjhvysocina.cz";
    $mail->FromName = "Pijem, jíme, hodnotíme (info@pjhvysocina.cz)";
    //$mail->addAddress('xixaom@centrum.cz', 'Joe User');     // Add a recipient
    $mail->addAddress($to);               // Name is optional
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    $mail->WordWrap = 80;                                 // Set word wrap to 50 characters
    //$mail->addAttachment('./prihlasky/' . $ID_PRIHLASKY . '.html');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);
    $mail->CharSet = "utf-8"; // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        
    }
}

function nahoru_fotky() {
    $result = dibi::query('SELECT * FROM [pf_fotky] WHERE [id] = %i', $_GET['nahoru']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] - 1;
    $result2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $dolu = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['nahoru']);
    $arr = array('poradi' => ($poradi_new + 1));
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $dolu);
}

function dolu_fotky() {
    $result = dibi::query('SELECT * FROM [pf_fotky] WHERE [id] = %i', $_GET['dolu']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] + 1;
    $result2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $nahoru = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['dolu']);
    $arr = array('poradi' => ($poradi_new - 1));
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $nahoru);
}

function nahoru_fotky_recenze() {
    $result = dibi::query('SELECT * FROM [pf_fotky] WHERE [id] = %i', $_GET['nahoru']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] - 1;
    $result2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $dolu = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['nahoru']);
    $arr = array('poradi' => ($poradi_new + 1));
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $dolu);
}

function dolu_fotky_recenze() {
    $result = dibi::query('SELECT * FROM [pf_fotky] WHERE [id] = %i', $_GET['dolu']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] + 1;
    $result2 = dibi::query('SELECT * FROM [pf_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $nahoru = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['dolu']);
    $arr = array('poradi' => ($poradi_new - 1));
    dibi::query('UPDATE [pf_fotky] SET', $arr, 'WHERE [id] = %i', $nahoru);
}

function show_banner() {
    $query = dibi::query("SELECT * FROM [pf_bannery] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
    $i = 0;
    $pole = array();
    while ($row = $query->fetch()) {
        $i++;
        $pole[$i] = $row[id];
    }

    if ($i > 0) {
        if ($i == 1)
            $vol_reklamu = 1;
        else
            $vol_reklamu = rand(1, $i);


        $querya = dibi::query("SELECT * FROM [pf_bannery] WHERE [id] = %i", $pole[$vol_reklamu]);
        $rowa = $querya->fetch();

        echo "<a href=\"./index.php?id=vyhledavani&ad=$rowa[sekce]&bd=profil&cd=$rowa[id_podnik]&banner=$rowa[id]\"><img class=\"\" width=\"100px\" src=\"$rowa[cil]\" title=\"" . get_nazev_podniku($rowa[id_podnik]) . ": $rowa[nazev]\" alt=\"" . get_nazev_podniku($rowa[id_podnik]) . ": $rowa[nazev]\" /></a>";

        $arr = array('zobrazeni' => $rowa[zobrazeni] + 1);
        dibi::query('UPDATE [pf_bannery] SET', $arr, 'WHERE [id] = %i', $pole[$vol_reklamu]);
    }
}

function spocist_banner() {
    if ($_GET[banner] != "") {
        $querya = dibi::query("SELECT * FROM [pf_bannery] WHERE [id] = %i", $_GET[banner]);
        $rowa = $querya->fetch();

        $arr = array('kliku' => $rowa[kliku] + 1);
        dibi::query('UPDATE [pf_bannery] SET', $arr, 'WHERE [id] = %i', $_GET[banner]);
    }
}

function create_udalost($typ, $id_relace, $jmeno_objektu) {
    $arr_temp = array('date' => time(), 'autor' => $_SESSION[jmeno], 'typ' => $typ, 'id_relace' => $id_relace, 'jmeno_objektu' => $jmeno_objektu);
    dibi::query('INSERT INTO [pf_udalosti]', $arr_temp);
}

function create_udalost_user($jmeno_objektu) {
    $arr_temp = array('date' => time(), 'autor' => $jmeno_objektu, 'typ' => "uzivatel", 'id_relace' => "", 'jmeno_objektu' => $jmeno_objektu);
    dibi::query('INSERT INTO [pf_udalosti]', $arr_temp);
}

function megasearch($retezec, $selector, $order, $asc_desc, $limit1, $limit2) {
    if ($order == "") {
        $order = "id_zaznamu";
    }

    if ($selector == "") {
        $selector = "id_zaznamu,lokalita,cislo_popisne,udalost,autorvydavatel,datace_1,typmedia,skladovano,klicova_slova,popis";
    }

    $pole_sekci = explode(",", $selector);

    $query = dibi::query("SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", $order, $asc_desc);
    if ($query->count() < 1) {
        echo "<div class=\"msg err\"><h2>Pro daný výtraz nemáme žádný záznam!</h2><p>Hledejte dál ;)</p></div>";
    } else {
        $count_naslych = 0;
        while ($row = $query->fetch()) {
            $zverejni = false;
            for ($i = 0; $i < count($pole_sekci) - 1; $i++) {
                if ($pole_sekci[$i] == "lokalita") {
                    $pole_lokalit = explode(",", $row[lokalita]);

                    if ($pole_lokalit[0] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[0]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                            
                        }
                    }
                    if ($pole_lokalit[1] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[1]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[2] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[2]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[3] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[3]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[4] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[4]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                } elseif ($pole_sekci[$i] == "autorvydavatel") {
                    $query2 = dibi::query('SELECT * FROM [pf_vydavatel] WHERE [id] = %i', $row[autorvydavatel]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[vydavatel], $retezec) !== false) {
                        $zverejni = true;
                    }
                } elseif ($pole_sekci[$i] == "typmedia") {
                    $query2 = dibi::query('SELECT * FROM [pf_typmedia] WHERE [id] = %i', $row[typmedia]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[typmedia], $retezec) !== false) {
                        $zverejni = true;
                    }
                } elseif ($pole_sekci[$i] == "skladovano") {
                    $query2 = dibi::query('SELECT * FROM [pf_archiv] WHERE [id] = %i', $row[skladovano]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[archiv], $retezec) !== false) {
                        $zverejni = true;
                    }
                } else {
                    if (strpos($row[$pole_sekci[$i]], $retezec) !== false) {
                        $zverejni = true;
                    }
                }
            }
            
            if($zverejni){
                $count_naslych++;
            }
            
        if (($count_naslych == $limit1 || $count_naslych == $limit2 || ($count_naslych > $limit1 && $count_naslych < $limit2)) && $zverejni) {
                    echo "<div class=\"block\">";
                    get_foto_of_db_foto($row[id]);
                    echo "<a href=\"./index.php?id=vyhledavani&ad=$row[id]\">id_zaznamu: #" . $row[id_zaznamu] . "</a><br />Přidáno: " . echo_time($row[date_zverejneni]);
                    echo "</div><div class=\"clear\"></div>";
            }
        }
    }
}

function megasearch_septem($retezec, $selector, $order, $asc_desc, $limit1, $limit2) {
    if ($order == "") {
        $order = "id_zaznamu";
    }

    if ($selector == "") {
        $selector = "id_zaznamu,lokalita,cislo_popisne,udalost,autorvydavatel,datace_1,typmedia,skladovano,klicova_slova,popis";
    }

    $pole_sekci = explode(",", $selector);

    $query = dibi::query("SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", $order, $asc_desc);
    if ($query->count() < 1) {
        echo "<div class=\"msg err\"><h2>Pro daný výtraz nemáme žádný záznam!</h2><p>Hledejte dál ;)</p></div>";
    } else {
        $count_naslych = 0;
        while ($row = $query->fetch()) {
            $zverejni = false;
            for ($i = 0; $i < count($pole_sekci) - 1; $i++) {
                if ($pole_sekci[$i] == "lokalita") {
                    $pole_lokalit = explode(",", $row[lokalita]);

                    if ($pole_lokalit[0] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[0]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                            
                        }
                    }
                    if ($pole_lokalit[1] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[1]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[2] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[2]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[3] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[3]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                    if ($pole_lokalit[4] != "0") {
                        $query2 = dibi::query('SELECT * FROM [pf_lokality] WHERE [id] = %i', $pole_lokalit[4]);
                        $row2 = $query2->fetch();
                        if (strpos($row2[lokalita], $retezec) !== false) {
                            $zverejni = true;
                        }
                    }
                } elseif ($pole_sekci[$i] == "autorvydavatel") {
                    $query2 = dibi::query('SELECT * FROM [pf_vydavatel] WHERE [id] = %i', $row[autorvydavatel]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[vydavatel], $retezec) !== false) {
                        $zverejni = true;
                    }
                } elseif ($pole_sekci[$i] == "typmedia") {
                    $query2 = dibi::query('SELECT * FROM [pf_typmedia] WHERE [id] = %i', $row[typmedia]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[typmedia], $retezec) !== false) {
                        $zverejni = true;
                    }
                } elseif ($pole_sekci[$i] == "skladovano") {
                    $query2 = dibi::query('SELECT * FROM [pf_archiv] WHERE [id] = %i', $row[skladovano]);
                    $row2 = $query2->fetch();
                    if (strpos($row2[archiv], $retezec) !== false) {
                        $zverejni = true;
                    }
                } else {
                    if (strpos($row[$pole_sekci[$i]], $retezec) !== false) {
                        $zverejni = true;
                    }
                }
            }
            
            if($zverejni){
                $count_naslych++;
            }
            
        if (($count_naslych == $limit1 || $count_naslych == $limit2 || ($count_naslych > $limit1 && $count_naslych < $limit2)) && $zverejni) {
                    echo "<div class=\"block\">";
                    get_foto_of_db_foto($row[id]);
                    echo "<a href=\"./index.php?id=vyhledavani&ad=$row[id]\">id_zaznamu: #" . $row[id_zaznamu] . "</a><br />Přidáno: " . echo_time($row[date_zverejneni]);
                    echo "</div><div class=\"clear\"></div>";
            }
        }
    }
}
?>