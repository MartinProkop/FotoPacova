<?php

function loguj($jmenolog, $heslolog, $zustatprihlasen) {
    if ($jmenolog == "")
        return 2;

    $hash_heslo = md5($heslolog);

    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $jmenolog, "AND [stav] = %s", "");
    while ($row = $query->fetch()) {
        $jmenotrue = $jmenolog;
        $heslotrue1 = $row[heslo_hash];
    }

    $query2 = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [email] = %s', $jmenolog, "AND [stav] = %s", "");
    while ($row2 = $query2->fetch()) {
        $jmenotrue = $row2[jmeno];
        $heslotrue2 = $row2[heslo_hash];
    }

    if ($hash_heslo == $heslotrue1 || $hash_heslo == $heslotrue2) {
        $_SESSION['jmeno'] = $jmenotrue;
        $_SESSION['heslo'] = $hash_heslo;

        //cookie
        if ($zustatprihlasen == "ano") {
            $token = md5(uniqid(time(), true));
            setcookie("pf_trvale_prihlaseni", "$_SESSION[jmeno]:$token", strtotime("+1 month"));

            $arr_uzivatel = array('token' => $token);
            dibi::query('UPDATE [pf_uzivatele] SET ', $arr_uzivatel, 'WHERE [jmeno] = %s', $_SESSION['jmeno']);
        }
        return 1;
    } else {
        $_SESSION['jmeno'] = "";
        $_SESSION['heslo'] = "";
        return 2;
    }
}

function login_check() {

    list($id_uzivatel, $token) = explode(":", $_COOKIE["pf_trvale_prihlaseni"], 2);

    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $id_uzivatel, "AND [stav] = %s", "");
    while ($row = $query->fetch()) {
        $token_db = $row[token];
        $jmeno = $row[jmeno];
        $hash_heslo = $row[heslo_hash];
    }

    if ($token_db == $token && $token_db != "") {
        $_SESSION['jmeno'] = $jmeno;
        $_SESSION['heslo'] = $hash_heslo;
        return TRUE;
    }

    if ($_SESSION['jmeno'] == "")
        return FALSE;

    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_SESSION['jmeno'], "AND [stav] = %s", "");
    while ($row = $query->fetch()) {
        $heslotrue = $row[heslo_hash];
    }

    if ($_SESSION['heslo'] == $heslotrue) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function admin_check() {
    $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno], "AND [stav] = %s", "");
    $row = $query->fetch();
    if ($row[admin] == "ano")
        return true;
    else
        return false;
}

function logout($jmeno) {
    //cookie
    $arr_uzivatel = array('token' => "");
    dibi::query('UPDATE [pf_uzivatele] SET ', $arr_uzivatel, 'WHERE [jmeno] = %s', $_SESSION['jmeno'], "AND [stav] = %s", "");
    if (isset($_COOKIE["pf_trvale_prihlaseni"])) {
        unset($_COOKIE["pf_trvale_prihlaseni"]);
        setcookie('pf_trvale_prihlaseni', '', time() - 3600); // empty value and old timestamp
    }

    $_SESSION['jmeno'] = "";
    $_SESSION['heslo'] = "";
}

if ($_POST['pokusolog'] == 1) {
    $_SESSION['jmeno'] = $_POST['jmeno'];
    $_SESSION['heslo'] = $_POST['heslo'];
}

//OSETRENI LOGINU
if ($_GET['action'] == "logout")
    logout($_SESSION['jmeno']);
//OSETRENI LOGINU
?>
