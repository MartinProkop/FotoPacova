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
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <title>Administrace | FOTOPACOVA</title>
        <?php
        // titulek
        if ($_GET[id] == null || $_GET[id] == "index") {
            $linksactiveadmin[index] = " class=\"active\"";
        } elseif ($_GET[id] == "databaze") {
            $linksactiveadmin[databaze] = " class=\"active\"";
        } elseif ($_GET[id] == "databazeaudia") {
            $linksactiveadmin[databazeaudia] = " class=\"active\"";
        } elseif ($_GET[id] == "databazevidea") {
            $linksactiveadmin[databazevidea] = " class=\"active\"";
        } elseif ($_GET[id] == "databazemap") {
            $linksactiveadmin[databazemap] = " class=\"active\"";
        } elseif ($_GET[id] == "clanky") {
            $linksactiveadmin[clanky] = " class=\"active\"";
        } elseif ($_GET[id] == "stranky") {
            $linksactiveadmin[stranky] = " class=\"active\"";
        }
        ?>
        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

        <!-- základ -->
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Martin Prokop" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link href="./img/favicon.png" rel="icon" type="image/gif" />
        <meta property="og:image" content="" />
        <meta property="og:url" content="" />
        <meta property="og:title" content="" />
        <meta property="og:description" content="" />        
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
            });</script>

        <!-- fancybox -->
        <script type="text/javascript" src="./lib/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
        <script type="text/javascript" src="./lib/fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

        <!-- inicializace fancyboxu -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('.fancybox').fancybox();
                $('.fancybox_iframe').fancybox({
                    width: '70%',
                    transitionIn: 'elastic',
                    transitionOut: 'elastic',
                    type: 'iframe',
                    autoScale: true,
                    helpers: {
                        title: {
                            type: 'float'
                        }
                    }
                });
                $('.fancybox-buttons').fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    prevEffect: 'none',
                    nextEffect: 'none',
                    closeBtn: false,
                    helpers: {
                        title: {
                            type: 'float'
                        },
                        buttons: {}
                    },
                    afterLoad: function () {
                        this.title = 'Obrázek ' + (this.index + 1) + ' z ' + this.group.length + (this.title ? ' - ' + this.title : '');
                    }
                });
            });</script>
    </head>
    <body>
        <?php include_once("./lib/analyticstracking.php"); ?>  
        <?php //show_banner();  ?>
        <!-- hlavní -->
        <div id="hlavni">
            <a name="top"></a>

            <!-- header -->
            <div id="header">
                <div class="wrapper">
                    <a href="./index.php"><img src="./img/logo.png" class="logo" alt="FOTOPACOVA"/></a>
                    <div id="main-menu">
                        <ul>
                            <li <?php echo $linksactive[index]; ?>><a href="./index.php?id=" title="">Domů</a></li>
                            <li <?php echo $linksactive[vyhledavani]; ?>><a href="./index.php?id=vyhledavani" title="">Fotografie</a></li>
                            <li <?php echo $linksactive[vyhledavaniaudio]; ?>><a href="./index.php?id=vyhledavaniaudio" title="">Audio</a></li>
                            <li <?php echo $linksactive[vyhledavanivideo]; ?>><a href="./index.php?id=vyhledavanivideo" title="">Video</a></li>
                            <li <?php echo $linksactive[vyhledavanimapy]; ?>><a href="./index.php?id=vyhledavanimapy" title="">Mapy</a></li>
                            <li <?php echo $linksactive[clanky]; ?>><a href="./index.php?id=clanky" title="">Články</a></li>
                            <li <?php echo $linksactive[oprojektu]; ?>><a href="./index.php?id=oprojektu" title="">O projektu</a></li>
                            <li <?php echo $linksactive[kontakt]; ?>><a href="./index.php?id=kontakt" title="">Kontakt</a></li>
                        </ul>
                    </div>
                    <div id="user-menu">
                        <ul>
                            <?php
                            if (login_check()) {
                                $query = dibi::query('SELECT * FROM [pf_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno], "AND [stav] = %s", "");
                                $row = $query->fetch();

                                if ($row[admin] == "ano") {
                                    ?>
                                    <li><a href="./formulare.php?id=uzivatel&amp;ad=<?php echo $row[id]; ?>" title="Profil uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>"><?php echo $_SESSION[jmeno]; ?></a></li>
                                    <li><a href="./administrace.php" title="Administrace">Administrace</a></li>
                                    <li><a href="./formulare.php?id=zpravy" title="Zprávy" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Zprávy <?php echo echo_pocet_novych_zprav(); ?></a></li>
                                    <li><a href="./formulare.php?id=nastaveniuzivatele" title="Nastavení uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Nastavení</a></li>
                                    <li><a href="./index.php?action=logout" title="Odhlásit">Odhlásit</a></li>

                                    <?php
                                } else {
                                    ?>
                                    <li><a href="./formulare.php?id=uzivatel&amp;ad=<?php echo $row[id]; ?>" title="Profil uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>"><?php echo $_SESSION[jmeno]; ?></a></li>
                                    <li><a href="./formulare.php?id=zpravy" title="Zprávy" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Zprávy <?php echo echo_pocet_novych_zprav(); ?></a></li>
                                    <li><a href="./formulare.php?id=nastaveniuzivatele" title="Nastavení uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Nastavení</a></li>
                                    <li><a href="./index.php?action=logout" title="Odhlásit">Odhlásit</a></li>                                        <?php
                                }
                            } else {
                                ?>
                                <li><a href="./formulare.php?id=registrace" title="Registrace" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Registrace</a></li>
                                <li><a href="./formulare.php?id=prihlaseni" title="Přihlášení" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Přihlášení</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <div id="content">

                <?php
                if (login_check() && admin_check()) {
                    ?>
                    <div id="admin-menu">
                        <ul>
                            <li <?php echo $linksactiveadmin[index]; ?>><a href="./administrace.php?id=" title="">Index</a></li>
                            <li <?php echo $linksactiveadmin[databaze]; ?>><a href="./administrace.php?id=databaze" title="">Databaze fotografií</a></li>
                            <li <?php echo $linksactiveadmin[databazeaudia]; ?>><a href="./administrace.php?id=databazeaudia" title="">Databaze audia</a></li>
                            <li <?php echo $linksactiveadmin[databazevidea]; ?>><a href="./administrace.php?id=databazevidea" title="">Databaze videa</a></li>
                            <li <?php echo $linksactiveadmin[databazemap]; ?>><a href="./administrace.php?id=databazemap" title="">Databaze map</a></li>
                            <li <?php echo $linksactiveadmin[clanky]; ?>><a href="./administrace.php?id=clanky" title="">Články</a></li>
                            <li <?php echo $linksactiveadmin[stranky]; ?>><a href="./administrace.php?id=stranky" title="">Stránky</a></li>
                        </ul>
                    </div>
                    <?php
                    if ($_GET[id] == null || $_GET[id] == "index") {
                        ?>
                        <h1>FOTO PACOVA - ADMINISTRACE</h1>
                        <p>Úvodní strana administrace, zatím tu nic není</p>
                        <?php
                    } elseif ($_GET[id] == "databaze") {
                        ?>
                        <h1>Databáze fotografií (<?php number_of_db_foto(); ?> položek)</h1>
                        <ul id="admin-menu2">
                            <li class=""><a href="./formulare_admin.php?id=novafotografie" class="fancybox_iframe fancybox.iframe">Nová fotografie</a></li>
                            <li class=""><a href="./formulare_admin.php?id=databazelokalit" class="fancybox_iframe fancybox.iframe">Databáze lokalit</a></li>
                            <li class=""><a href="./formulare_admin.php?id=databazevydavatelu" class="fancybox_iframe fancybox.iframe">Databáze vydavatelů</a></li>
                            <li class=""><a href="./formulare_admin.php?id=databazetypumedia" class="fancybox_iframe fancybox.iframe">Databáze typů média</a></li>
                            <li class=""><a href="./formulare_admin.php?id=databazearchivu" class="fancybox_iframe fancybox.iframe">Databáze archivů</a></li>


                        </ul>
                        <?php
                        if ($_GET[smazat] != "") {
                            $arr = array('zverejnit' => 'ne');
                            dibi::query('UPDATE [pf_databaze] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                            $query = dibi::query('DELETE FROM [pf_databaze_relace] WHERE [id_jedna] = %i', $_GET[smazat], 'OR [id_dva] = %i', $_GET[smazat]);

                            echo "<div class=\"msg information\"><h2>Fotografie smazána!</h2><p></p></div>";
                        }

                        if ($_GET[zverejnit] != "") {
                            $arr = array('zverejnit' => 'ano');
                            dibi::query('UPDATE [pf_databaze] SET ', $arr, 'WHERE [id] = %i', $_GET[zverejnit]);

                            foto_was_edited($_GET[zverejnit]);
                            create_udalost("fotka", $_GET[zverejnit], get_idzaznamu_fotka($_GET[zverejnit]));

                            echo "<div class=\"msg information\"><h2>Fotografie byla zveřejněna!</h2><p></p></div>";
                        }

                        if ($_GET[skryt] != "") {
                            $arr = array('zverejnit' => 'skryt');
                            dibi::query('UPDATE [pf_databaze] SET ', $arr, 'WHERE [id] = %i', $_GET[skryt]);

                            foto_was_edited($_GET[skryt]);

                            echo "<div class=\"msg information\"><h2>Fotografie byla skryta pro uživatele!</h2><p></p></div>";
                        }

                        $nr = dibi::query('SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s', "ano", 'OR [zverejnit] = %s', "skryt", 'ORDER BY %by', "id", "DESC")->count();
                        if ($nr > 0) {
                            if (isset($_GET['pn'])) { // Get pn from URL vars if it is present
                                $pn = preg_replace('#[^0-9]#i', '', $_GET['pn']);
                            } else {
                                $pn = 1;
                            }

                            $itemsPerPage = 10;

                            $lastPage = ceil($nr / $itemsPerPage);


                            if ($pn < 0) {
                                $pn = 1; // force if to be 1
                            } else if ($pn > $lastPage) {
                                $pn = $lastPage;
                            }
                            $centerPages = "";
                            $sub1 = $pn - 1;
                            $sub2 = $pn - 2;
                            $add1 = $pn + 1;
                            $add2 = $pn + 2;
                            if ($pn == 1) {
                                $centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                            } else if ($pn == $lastPage) {
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                                $centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
                            } else if ($pn > 2 && $pn < ($lastPage - 1)) {
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $sub2 . '">' . $sub2 . '</a> &nbsp;';
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                                $centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $add2 . '">' . $add2 . '</a> &nbsp;';
                            } else if ($pn > 1 && $pn < $lastPage) {
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $sub1 . '">' . $sub1 . '</a> &nbsp;';
                                $centerPages .= '&nbsp; <span class="pagNumActive">' . $pn . '</span> &nbsp;';
                                $centerPages .= '&nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $add1 . '">' . $add1 . '</a> &nbsp;';
                            }

                            $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;

                            $paginationDisplay = "";

                            if ($lastPage != "1") {

                                $paginationDisplay .= 'Strana <strong>' . $pn . '</strong> z ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';

                                if ($pn != 1) {
                                    $previous = $pn - 1;
                                    $paginationDisplay .= '&nbsp;  <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $previous . '"> Zpět</a> ';
                                }

                                $paginationDisplay .= '<span class="paginationNumbers">' . $centerPages . '</span>';

                                if ($pn != $lastPage) {
                                    $nextPage = $pn + 1;
                                    $paginationDisplay .= '&nbsp;  <a href="' . $_SERVER['PHP_SELF'] . '?id=databaze&pn=' . $nextPage . '"> Dopředu</a> ';
                                }
                            }

                            echo $paginationDisplay;

                            $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [zverejnit] = %s', "ano", 'OR [zverejnit] = %s', "skryt", 'ORDER BY %by', "id", "DESC", $limit);
                            while ($row = $query->fetch()) {
                                echo "<div class=\"block\">";
                                get_foto_of_db_foto($row[id]);
                                echo "#" . $row[id_zaznamu] . "<br />";
                                echo "Přidáno (" . echo_time($row[date_zverejneni]) . "): <a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row[zadavatel_admin]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row[zadavatel_admin]</a> <br />";
                                echo "Poslední úprava (" . echo_time($row[date_uprava]) . "): <a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row[zadavatel_admin_uprava]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row[zadavatel_admin_uprava]</a><br />";

                                echo "<a href=\"./formulare_admin.php?id=upravitrelace&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">upravit relace (" . number_of_relations($row[id]) . ")</a><br /> ";

                                echo "<a href=\"./formulare_admin.php?id=upravitkomentare&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">upravit komentáře (" . number_of_comments($row[id]) . ")</a><br /> ";

                                echo "<a href=\"./formulare_admin.php?id=upravitkomentareadmins&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">administrátorské komentáře (" . number_of_comments_admins($row[id]) . ")</a><br /> ";
                                
                                
                                if ($row[zverejnit] == "skryt") {
                                    echo "<a href=\"administrace.php?id=databaze&zverejnit=$row[id]\">&spades; zveřejnit</a><br /> ";
                                } elseif ($row[zverejnit] == "ano") {
                                    echo "<a href=\"administrace.php?id=databaze&skryt=$row[id]\">&diams; skrýt pro návštěvníky</a><br /> ";
                                }

                                echo "<a href=\"./formulare_admin.php?id=upravitfotografie&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">&harr; upravit</a> | ";

                                echo "<a href=\"administrace.php?id=databaze&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat záznam v databázi?'))
                                     location.href = './administrace.php?id=databaze&smazat=$row[id]';
                                     return(false);\">&dagger; smazat</a>";
                                echo "</div><div class=\"clear\"></div>";
                            }

                            echo $paginationDisplay;
                        }
                        ?>

                        <?php
                    } elseif ($_GET[id] == "databazeaudia") {
                        ?>
                        <h1>DATABAZE AUDIA</h1>

                        <?php
                    } elseif ($_GET[id] == "clanky") {
                        ?>
                        <h1>Články</h1>
                        <ul id="admin-menu2">
                            <li class=""><a href="./formulare_admin.php?id=novyclanek" class="fancybox_iframe fancybox.iframe">Nový článek</a></li>
                        </ul>
                        <?php
                        if ($_GET[smazat] != "") {

                            $arr = array('zverejnit' => 'ne');
                            dibi::query('UPDATE [pf_clanky] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                            echo "<div class=\"msg information\"><h2>Článek smazán!</h2><p></p></div>";
                        }

                        $query = dibi::query('SELECT * FROM [pf_clanky] WHERE [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
                        while ($row = $query->fetch()) {
                            echo "<div class=\"block\">";
                            get_foto_of_clanek($row[id]);
                            echo "<a href=\"./index.php?id=clanky&ad=$row[id]\">" . $row[nazev] . "</a><br />";
                            if ($row[id_relace] != 0) {
                                if ($row[typ] == "fotka")
                                    echo "relace na fotografii <a href=\"./index.php?id=vyhledavani&ad=$row[id_relace]\">#" . get_idzaznamu_fotka($row[id_relace]) . "</a><br />";
                            }
                            echo echo_time($row[date]);


                            echo "<br />";

                            echo "<a href=\"./formulare_admin.php?id=upravitclanek&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">&harr; upravit</a> ";

                            echo "<a href=\"administrace.php?id=clanky&smazat=$row[id]\" onclick=\"if (confirm('Skutečně smazat článek?'))
                                                                            location.href = './administrace.php?id=clanky&smazat=$row[id]';
                                                                        return(false);\">&dagger; smazat</a>";
                            echo "</div><div class=\"clear\"></div>";
                        }
                        ?>
                        <?php
                    } elseif ($_GET[id] == "stranky") {
                        ?>
                        <h1>Stránky</h1>

                        <ul id="admin-menu2">
                            <?php
                            if ($_GET[ad] == "")
                                $_GET[ad] = "kontakt";

                            if ($_GET[ad] == "kontakt") {
                                echo "<li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakt</a></li>"
                                . "<li><a href=\"./administrace.php?id=stranky&ad=oprojektu\">O projektu</a></li>";
                            } elseif ($_GET[ad] == "oprojektu") {
                                echo "<li class=\"\"><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakt</a></li>"
                                . "<li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=oprojektu\">O projektu</a></li>";
                            }
                            ?>
                        </ul>


                        <?php
                        if ($_GET[send] == "ano") {
                            $arr = array('obsah' => $_POST[textarea]);
                            dibi::query('UPDATE [pf_stranky] SET ', $arr, 'WHERE [sekce] = %s', $_GET[ad]);
                        }

                        $query = dibi::query('SELECT * FROM [pf_stranky] WHERE [sekce] = %s', $_GET[ad]);
                        $row = $query->fetch();
                        $obsah = $row[obsah];
                        ?>

                        <div id="okno_form">  
                            <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                            <form id="formul" name="formul" method="post" action="./administrace.php?id=stranky&ad=<?php echo $_GET[ad]; ?>&send=ano" >
                                <dl class="inline">
                                    <dt><label for="textarea">Obsah stránky *</label></dt>
                                    <dd><textarea id="textarea" name="textarea" required><?php echo $obsah; ?></textarea></dd>
                                    <dt><label for="">(*) označuje povinné položky</label></dt>
                                    <dd><input type="submit" class="button" id="submit1" value="Upravit">
                                </dl>
                            </form>  
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                } else {
                    ?>
                    <div class="msg err"><h2>Nemáš tu co dělat! Nejsi admin!</h2><p>Jdi na stránky <a href="./index.php">FOTOPACOVA</a>!</p></div>
                    <?php
                }
                ?>                
            </div>

            <div id="clanky">
                <h2>Články</h2>

                <?php
                $query = dibi::query('SELECT * FROM [pf_clanky] WHERE [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC LIMIT 0, 2");
                while ($row = $query->fetch()) {
                    echo "<div class=\"block\">";
                    get_foto_of_clanek_title($row[id]);
                    echo "<a href=\"./index.php?id=clanky&ad=$row[id]\">" . $row[nazev] . "</a><br />";
                    if ($row[id_relace] != 0) {
                        if ($row[typ] == "fotka")
                            echo "relace na fotografii <a href=\"./index.php?id=vyhledavani&ad=$row[id_relace]\">#" . get_idzaznamu_fotka($row[id_relace]) . "</a><br />";
                    }
                    echo echo_time($row[date]);
                    echo "</div><div class=\"clear\"></div>";
                }
                ?>

            </div>

            <div id="udalosti">
                <h2>Události</h2>

                <?php
                $query = dibi::query('SELECT * FROM [pf_udalosti] ORDER BY %by', "id", "DESC LIMIT 0, 10");
                while ($row = $query->fetch()) {
                    echo "<div class=\"block\">";
                    echo echo_time($row[date]) . " ";
                    echo "<a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row[autor]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row[autor]</a>";
                    if ($row[typ] == "clanek")
                        echo " přidal článek <a href=\"./index.php?id=clanky&ad=$row[id_relace]\">" . $row[jmeno_objektu] . "</a>";
                    elseif ($row[typ] == "fotka")
                        echo " přidal fotografii <a href=\"./index.php?id=vyhledavani&ad=$row[id_relace]\">#" . $row[jmeno_objektu] . "</a>";
                    elseif ($row[typ] == "komentar")
                        echo " přidal komentář fotografie <a href=\"./index.php?id=vyhledavani&ad=$row[id_relace]\">#" . $row[jmeno_objektu] . "</a>";
                    elseif ($row[typ] == "uzivatel")
                        echo " se zaregistroval</a>";
                    echo ". ";
                    echo "</div><div class=\"clear\"></div>";
                }
                ?>

            </div>    
            <div class="clear"></div>

            <div id="footer">
                <div class="left">
                    Copyright © 2015 - FOTOPACOVA
                </div>
                <div class="right">
                    <ul>
                        <li <?php echo $linksactive[index]; ?>><a href="./index.php?id=" title="">Domů</a></li>
                        <li <?php echo $linksactive[vyhledavani]; ?>><a href="./index.php?id=vyhledavani" title="">Fotografie</a></li>
                        <li <?php echo $linksactive[vyhledavaniaudio]; ?>><a href="./index.php?id=vyhledavaniaudio" title="">Audio</a></li>
                        <li <?php echo $linksactive[vyhledavanivideo]; ?>><a href="./index.php?id=vyhledavanivideo" title="">Video</a></li>
                        <li <?php echo $linksactive[vyhledavanimapy]; ?>><a href="./index.php?id=vyhledavanimapy" title="">Mapy</a></li>
                        <li <?php echo $linksactive[clanky]; ?>><a href="./index.php?id=clanky" title="">Články</a></li>
                        <li <?php echo $linksactive[oprojektu]; ?>><a href="./index.php?id=oprojektu" title="">O projektu</a></li>
                        <li <?php echo $linksactive[kontakt]; ?>><a href="./index.php?id=kontakt" title="">Kontakt</a></li>
                        <li><a href="#top">Zpátky nahoru &raquo;</a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </body>
</html>
