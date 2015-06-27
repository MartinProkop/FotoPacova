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
        <?php
        // jazyk
        if ($_GET[lang] == null) {
            $_GET[lang] = "cs";
        }

        // titulek
        if ($_GET[id] == "") {
            $linksactive[home] = " class=\"active\"";
        } elseif ($_GET[id] == "gurmani") {
            $linksactive[gurmani] = " class=\"active\"";
        } elseif ($_GET[id] == "podniky") {
            $linksactive[podniky] = " class=\"active\"";
        } elseif ($_GET[id] == "recenze") {
            $linksactive[recenze] = " class=\"active\"";
        } elseif ($_GET[id] == "aktuality") {
            $linksactive[aktuality] = " class=\"active\"";
        } elseif ($_GET[id] == "stranky") {
            $linksactive[stranky] = " class=\"active\"";
        } elseif ($_GET[id] == "banner") {
            $linksactive[banner] = " class=\"active\"";
        }
        ?>
        <title>ADMINISTRACE | PIJEM, JÍME, HODNOTÍME</title>

        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>

        <!-- základ -->
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="martin prokop" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="Pijem, jíme, hodnotíme, ">
        <meta name="description" content="">
        <link type="" href="./img/favicon.png" rel="shortcut icon">
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
                    width: '80%',
                    transitionIn: 'elastic',
                    transitionOut: 'elastic',
                    type: 'iframe',
                    autoScale: false,
                    helpers: {
                        title: {
                            type: 'float'
                        }
                    }
                });

                $('.fancybox_iframe_big').fancybox({
                    width: '95%',
                    height: '95%',
                    autoSize: true,
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
            });
        </script>
    </head>
    <body>
        <div id="hlavni">
            <div>
                <!-- header -->
                <div id="header">
                    <div class="wrapper_admin">
                        <a href="./index.php"><img src="./img/logo.png" class="logo"/></a>
                        <!-- <a href="./index.php" class="logotext">PIJEM, JÍME, HODNOTÍME</a> -->
                        <div id="main-menu">
                            <ul>
                                <li <?php echo $linksactive[home]; ?>><a href="./administrace.php?id=">Home</a></li>
                                <li <?php echo $linksactive[gurmani]; ?>><a href="./administrace.php?id=gurmani">Gurmáni</a></li>
                                <li <?php echo $linksactive[podniky]; ?>><a href="./administrace.php?id=podniky">Podniky</a></li>
                                <li <?php echo $linksactive[recenze]; ?>><a href="./administrace.php?id=recenze">Recenze</a></li>
                     <!--           <li <?php echo $linksactive[banner]; ?>><a href="./administrace.php?id=banner">Reklama</a></li> -->
                                <li <?php echo $linksactive[aktuality]; ?>><a href="./administrace.php?id=aktuality">Aktuality</a></li>
                                <li <?php echo $linksactive[stranky]; ?>><a href="./administrace.php?id=stranky">Stránky</a></li>                                
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <!-- user -->
                <div class="wrapper_admin">
                    <div id="user">
                        <?php
                        if (login_check()) {
                            $query = dibi::query('SELECT * FROM [fp_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno]);
                            $row = $query->fetch();

                            if (admin_check()) {
                                ?>
                                <a href="./formulare.php?id=gurman&ad=<?php echo $row[id]; ?>" title="Profil uživatele" class="gurmani fancybox_iframe fancybox.iframe"><img src="./img/gurmani.png"> <?php echo $_SESSION[jmeno]; ?></a><img src="./img/gurmani-carka.png"><span class="prihlaseni_text"><a href="./index.php" title="Zpět na stránky">Zpět na web</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./administrace.php" title="Administrace">Administrace</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=nastaveniuzivatele" title="Nastavení uživatele" class="fancybox_iframe fancybox.iframe">Nastavení</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=zpravy" title="Zprávy" class="fancybox_iframe fancybox.iframe">Zprávy <?php echo echo_pocet_novych_zprav(); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=logout" title="Odhlásit">Odhlásit</a></span>

                                <?php
                            }
                        } else {
                            ?>
                            <a href="./formulare.php?id=prihlaseni" title="Přihlášení" class="gurmani fancybox_iframe fancybox.iframe"><img src="./img/gurmani.png"></a><img src="./img/gurmani-carka.png"><span class="prihlaseni_text"><a href="./formulare.php?id=prihlaseni&redirect=<?php echo $_SERVER['REQUEST_URI']; ?>" title="Přihlášení" class="fancybox_iframe fancybox.iframe">Přihlášení</a></span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <!-- zacina content -->
                <div id="content">
                    <?php
                    if (login_check() && admin_check()) {
                        if ($_GET[id] == "") {
                            ?>
                            <h1 id="content_logo_nadpis">Nevyřízené požadavky</h1>
                            <h2>Podniky čekající na schválení</h2>
                            <div class="wrapper">   
                                <?php
                                $query = dibi::query("SELECT * FROM [fp_podnik] WHERE [zverejnit] = %s", "ne", "ORDER BY %by", "id", "ASC");
                                if ($query->count() == 0)
                                    echo "<div class=\"msg information\"><h2>Nečeká na Vás žádná neschválený podnik!</h2></div>";
                                $i = 1;
                                while ($row = $query->fetch()) {
                                    $query_recenze = dibi::query("SELECT * FROM [fp_recenze] WHERE [id_podnik] = %i", $row[id]);
                                    $row_recenze = $query_recenze->fetch();
                                    ?>
                                    <div class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <a class="fancybox fancybox_iframe_big" href="./formulare_admin.php?id=schvalit&ad=<?php echo $row[id]; ?>" title="<?php echo $row[nazev]; ?>">
                                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_2">
                                                        <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt=""/>
                                                    </div>
                                                    <div class="grid_4 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                                            <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="" height="16px"/></span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="grid_5 ">
                                                    <p class="card_text_bez_paddingu cerna_barva text_11px"><?php echo substr(nl2br($row[obsah]), 0, 100); ?>... 
                                                        <span class="recenze_autor"><a href="./formulare.php?id=gurman&ad=<?php echo $row_recenze[id_autor]; ?>" class="fancybox_iframe_big fancybox.iframe" title="<?php echo $row_recenze[nazevautor]; ?>"><?php echo $row_recenze[nazevautor]; ?> <span class="text_11px">(<?php echo echo_date($row_recenze[date]); ?>)</span></a></span>

                                                    </p>
                                                </div>                                                

                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                 
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>    
                            <!--
                            <h2>Recenze čekající na schválení</h2>
                            <div class="wrapper">   
                            <?php
//                                $query = dibi::query("SELECT * FROM [fp_recenze] WHERE [zverejnit] = %s", "ne", "ORDER BY %by", "id", "ASC");
//                                if ($query->count() == 0)
//                                    echo "<div class=\"msg information\"><h2>Nečeká na Vás žádná neschválená recenze!</h2></div>";
//                                $i = 1;
//                                $x = 1;
//                                while ($row = $query->fetch()) {
//                                    $query_podnik = dibi::query("SELECT * FROM [fp_podnik] WHERE [id] = %i", $row[id_podnik]);
//                                    $row_podnik = $query_podnik->fetch();
//                                    if ($row_podnik[zverejnit] == "ano") {
                            ?>
                                        <div class="card_prehledy70 margin_top3">
                                            <div class="card_vnitrni5">
                                                <div>
                                                    <a class="fancybox fancybox_iframe_big" href="./formulare_admin.php?id=schvalit_recenze&ad=<?php //echo $row[id];         ?>" title="<?php //echo $row[nazevpodnik];         ?>">
                                                        <div class="grid_1 card_prehledy_border70_bez_cary">
                                                            <div class="card_vertical_align15 ">
                                                                <span class="card_text_cislo cerna_barva"><?php //echo $i;         ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="grid_2">
                                                            <img class="card_obrazek_podnik100" src="<?php //echo get_foto_of_podnik($row[id_podnik]);         ?>" alt=""/>
                                                        </div>
                                                        <div class="grid_4 card_prehledy_border70">
                                                            <div class="card_vertical_align10">
                                                                <span class="card_text_nazev cerna_barva"><?php //echo $row[nazevpodnik];         ?></span><br />
                                                                <span class="card_text_procenta "><?php //echo $row[hodnoceni];         ?>/10 <img class="card_obrazek_procent" src="<?php //echo get_hveznicky_na_cislo($row[hodnoceni]);         ?>" alt="" height="16px"/></span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="grid_5 ">
                                                        <p class="card_text_bez_paddingu cerna_barva text_11px"><?php //echo substr(nl2br($row[obsah]), 0, 100);         ?>... 
                                                            <span class="recenze_autor"><a href="./formulare.php?id=gurman&ad=<?php //echo $row[id_autor];         ?>" class="fancybox_iframe_big fancybox.iframe" title="<?php //echo $row[nazevautor];         ?>"><?php //echo $row[nazevautor];         ?> <span class="text_11px">(<?php echo echo_date($row[date]); ?>)</span></a></span>
                                                        </p>
                                                    </div>                                                

                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>                                 
                            <?php
//                                        $x++;
//                                    }
//                                    $i++;
//                                }
//                                if ($i > 1 && $x == 1)
//                                    echo "<div class=\"msg err\"><h2>Nečeká na Vás žádná neschválená recenze!</h2><p>Ale čekají na Vás recenze, které je třeba schválit v rámci přidání nových podniků.</p></div>";
                            ?>
                            </div>  -->
                            <?php
                        } elseif ($_GET[id] == "gurmani") {
                            echo "<h1 id=\"content_logo_nadpis\">Gurmáni</h1>";

                            if ($_GET[smazat] != "") {

                                $arr = array('stav' => 'smazan');
                                dibi::query('UPDATE [fp_uzivatele] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                                echo "<div class=\"msg information\"><h2>Uživatel smazán!</h2><p></p></div>";
                            }

                            if ($_GET[pismeno] == "vse") {
                                $linksactive_vyhledavani_pismeno[vse] = "active first";
                            } elseif ($_GET[pismeno] == "a") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[a] = "active";
                            } elseif ($_GET[pismeno] == "b") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[b] = "active";
                            } elseif ($_GET[pismeno] == "c") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[c] = "active";
                            } elseif ($_GET[pismeno] == "d") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[d] = "active";
                            } elseif ($_GET[pismeno] == "e") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[e] = "active";
                            } elseif ($_GET[pismeno] == "f") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[f] = "active";
                            } elseif ($_GET[pismeno] == "g") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[g] = "active";
                            } elseif ($_GET[pismeno] == "h") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[h] = "active";
                            } elseif ($_GET[pismeno] == "i") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[i] = "active";
                            } elseif ($_GET[pismeno] == "j") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[j] = "active";
                            } elseif ($_GET[pismeno] == "k") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[k] = "active";
                            } elseif ($_GET[pismeno] == "l") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[l] = "active";
                            } elseif ($_GET[pismeno] == "m") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[m] = "active";
                            } elseif ($_GET[pismeno] == "n") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[n] = "active";
                            } elseif ($_GET[pismeno] == "o") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[o] = "active";
                            } elseif ($_GET[pismeno] == "p") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[p] = "active";
                            } elseif ($_GET[pismeno] == "q") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[q] = "active";
                            } elseif ($_GET[pismeno] == "r") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[r] = "active";
                            } elseif ($_GET[pismeno] == "s") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[s] = "active";
                            } elseif ($_GET[pismeno] == "t") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[t] = "active";
                            } elseif ($_GET[pismeno] == "u") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[u] = "active";
                            } elseif ($_GET[pismeno] == "v") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[v] = "active";
                            } elseif ($_GET[pismeno] == "w") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[w] = "active";
                            } elseif ($_GET[pismeno] == "x") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[x] = "active";
                            } elseif ($_GET[pismeno] == "y") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[y] = "active";
                            } elseif ($_GET[pismeno] == "z") {
                                $linksactive_vyhledavani_pismeno[vse] = "first";
                                $linksactive_vyhledavani_pismeno[z] = "active";
                            }
                            ?>
                            <div class="wrapper2">
                                <div id="searching_bar_abeceda-menu">
                                    <form id="formul" name="formul" method="post" action="./administrace.php?id=gurmani" >
                                        <ul>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[vse]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=vse" title="">VŠE</a></li>  
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[a]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=a" title="">A</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[b]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=b" title="">B</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[c]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=c" title="">C</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[d]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=d" title="">D</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[e]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=e" title="">E</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[f]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=f" title="">F</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[g]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=g" title="">G</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[h]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=h" title="">H</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[i]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=i" title="">I</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[j]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=j" title="">J</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[k]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=k" title="">K</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[l]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=l" title="">L</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[m]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=m" title="">M</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[n]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=n" title="">N</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[o]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=o" title="">O</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[p]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=p" title="">P</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[q]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=q" title="">Q</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[r]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=r" title="">R</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[s]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=s" title="">S</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[t]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=t" title="">T</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[u]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=u" title="">U</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[v]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=v" title="">V</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[w]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=w" title="">W</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[x]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=x" title="">X</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[y]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=y" title="">Y</a></li>
                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[z]; ?>" ><a href="./administrace.php?id=gurmani&pismeno=z" title="">Z</a></li>      
                                            <li><input type="text" class="input width_search_field" id="retezec" name="retezec" value="<?php echo $_POST[retezec]; ?>" placeholder="Zadej jméno"></li>
                                            <li><input type="submit" class="najdi_link" id="text-input-1-submit" value="Najdi"></li>
                                        </ul>
                                    </form>
                                    <div class="clear"></div>   
                                </div>
                            </div>
                            <div id="vyhledavani" class="margin_top1">
                                <div class="wrapper">   
                                    <div>
                                        <?php
                                        if ($_GET[pismeno] != "") {
                                            if ($_GET[pismeno] == "vse") {
                                                $query = dibi::query("SELECT * FROM [fp_uzivatele]", "ORDER BY %by", "jmeno", "ASC");
                                            } else {
                                                $query = dibi::query("SELECT * FROM [fp_uzivatele] WHERE [jmeno] LIKE %s", $_GET[pismeno] . '%', "ORDER BY %by", "jmeno", "ASC");
                                            }
                                        } elseif ($_POST[retezec] != "") {
                                            $query = dibi::query("SELECT * FROM [fp_uzivatele] WHERE [jmeno] LIKE %s", '%' . $_POST[retezec] . '%', "ORDER BY %by", "jmeno", "ASC");
                                        }
                                        $i = 1;
                                        if ($query->count() == 0)
                                            echo "<div class=\"msg err\"><h2>Je nám líto, ale není zde gurmán, který by odpovídal zadání!</h2><p>Hledejte dále.</p></div>";
                                        while ($row = $query->fetch()) {
                                            ?>
                                            <div class="card_prehledy60 margin_top3">
                                                <div class="card_vnitrni5">
                                                    <div>
                                                        <a href="./formulare.php?id=gurman&ad=<?php echo $row[id]; ?>" class="fancybox_iframe_big fancybox.iframe" title="Gurmán - <?php echo $row[jmeno]; ?>">
                                                            <div class="grid_1 card_prehledy_border50">
                                                                <div class="card_vertical_align10 ">
                                                                    <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="grid_1">
                                                                <img class="card_obrazek_podnik80" src="<?php echo get_foto_of_user($row[id]); ?>" alt=""/>
                                                            </div>
                                                            <div class="grid_3 card_prehledy_border50">
                                                                <div class="card_vertical_align5">
                                                                    <span class="card_text_nazev cerna_barva"><?php echo $row[jmeno]; ?></span><br />
                                                                    <span class="card_text_procenta cerna_barva "><?php echo echo_hodnost($row[hodnost]); ?></span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div class="grid_6 card_prehledy_border50">
                                                            <p class="card_text_bez_paddingu cerna_barva text_11px"><?php echo $row[popis]; ?></p>
                                                        </div>
                                                        <div class="grid_1 card_prehledy_border50">
                                                            <p class="card_text_bez_paddingu cerna_barva text_11px"><a href="administrace.php?id=gurmani&smazat=<?php echo $row[id]; ?>" onclick="if (confirm('Skutečně smazat uživatele \n \n<?php echo $row[jmeno]; ?>?'))
                                                                                    location.href = './administrace.php?id=gurmani&smazat=<?php echo $row[id]; ?>';
                                                                                return(false);">smazat</a></p>
                                                        </div> 
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            </div>                                
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </div>                    
                                </div>
                                <div class="clear"></div>   
                            </div>
                            <?php
                        } elseif ($_GET[id] == "podniky") {
                            ?>
                            <h1 id="content_logo_nadpis">Podniky</h1>
                            <?php
                            if ($_GET[smazat] != "") {

                                $arr = array('zverejnit' => 'smazat');
                                dibi::query('UPDATE [fp_podnik] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                                dibi::query('UPDATE [fp_recenze] SET ', $arr, 'WHERE [id_podnik] = %i', $_GET[smazat]);

                                $query_chci_id = dibi::query('SELECT * FROM [fp_podnik] WHERE [id] = %i', $_GET[smazat]);
                                $row_chci_id = $query_chci_id->fetch();

                                $query_uprav_pocty = dibi::query('SELECT * FROM [fp_recenze] WHERE [id_podnik] = %i', $_GET[smazat]);
                                while ($row_uprav_pocty = $query_uprav_pocty->fetch()) {

                                    $query_uzivat = dibi::query('SELECT * FROM [fp_uzivatele] WHERE [id] = %i', $row_uprav_pocty[id_autor]);
                                    $row_uzivat = $query_uzivat->fetch();

                                    $arr_uzivat = array('hodnost' => $row_uzivat[hodnost] - 1);
                                    dibi::query('UPDATE [fp_uzivatele] SET', $arr_uzivat, ' WHERE [id] = %i', $row_uprav_pocty[id_autor]);
                                }

                                $arr_temp = array('id_cil' => get_id_autora_podniku($_GET[smazat]), 'date' => time(), 'obsah' => "<em>Smazali jsme podnik <u>" . $row_chci_id[nazev] . "</u></em>.", 'stav' => "nova");
                                dibi::query('INSERT INTO [fp_zpravy]', $arr_temp);

                                $body = "Dobrý den,\n\n<em>Smazali jsme podnik <u>" . $row_chci_id[nazev] . "</u></em>, který jste přida do naší databáze. \n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                                send_mail_kovar(get_email_uzivatele(get_id_autora_podniku($_GET[smazat])), "Odmítnutá recene na serveru pjhvysocina.cz", $body);


                                echo "<div class=\"msg information\"><h2>Podnik smazán!</h2><p></p></div>";
                            }
                            if ($_GET[oddoporucit] != "") {

                                $arr = array('doporucujeme' => '');
                                dibi::query('UPDATE [fp_podnik] SET ', $arr, 'WHERE [id] = %i', $_GET[oddoporucit]);


                                echo "<div class=\"msg information\"><h2>Zrušeno doporučení podniku!</h2><p></p></div>";
                            }
                            ?>
                            <div class="wrapper4">
                                <div id="sezazeni">
                                    <ul>
                                        <?php
                                        if ($_GET[ad] == "") {
                                            echo "<li class=\"first\">Podniky:</li><li><a class=\"fancybox_iframe_big fancybox.iframe\" href=\"./formulare_admin.php?id=novypodnik\">Nový podnik</a></li><li><a href=\"./administrace.php?id=podniky&ad=restaurace\">Restaurace</a></li><li><a href=\"./administrace.php?id=podniky&ad=kavarny\">Kavárny</a></li>";
                                        } elseif ($_GET[ad] == "restaurace") {
                                            echo "<li class=\"first\">Podniky:</li><li><a class=\"fancybox_iframe_big fancybox.iframe\" href=\"./formulare_admin.php?id=novypodnik&ad=restaurace\">Nový podnik</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=restaurace\">Restaurace</a></li><li><a href=\"./administrace.php?id=podniky&ad=kavarny\">Kavárny</a></li>";
                                        } elseif ($_GET[ad] == "kavarny") {
                                            echo "<li class=\"first\">Podniky:</li><li><a class=\"fancybox_iframe_big fancybox.iframe\" href=\"./formulare_admin.php?id=novypodnik&ad=kavarny\">Nový podnik</a></li><li><a href=\"./administrace.php?id=podniky&ad=restaurace\">Restaurace</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=kavarny\">Kavárny</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>  
                                <?php
                                if ($_GET[ad] != "") {
                                    ?>
                                </div>  
                                <div class="wrapper3">
                                    <div id="sezazeni">
                                        <a name="hodnoceni"></a>
                                        <ul>
                                            <?php
                                            if (($_GET[order] == "nazev" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                $_GET[order] = "nazev";
                                                $_GET[by] = "desc";
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=asc\">Názvu podniku <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Vložení</a></li>";
                                            } elseif (($_GET[order] == "nazev" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Vložení</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=asc\">Hodnocení <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Vložení</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Vložení</a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=asc\">Vložení <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Vložení <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li>";
                                            }
//                                            elseif (($_GET[order] == "vlozil" && $_GET[by] == "desc") || $_GET[order] == "") {
//                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=vlozil&by=asc\">Autora <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li>";
//                                            } elseif (($_GET[order] == "vlozil" && $_GET[by] == "asc") || $_GET[order] == "") {
//                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=nazev&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li class=\"active\"><a href=\"./administrace.php?id=podniky&ad=$_GET[ad]&order=vlozil&by=desc\">Autora <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li>";
//                                            }
                                            ?>
                                        </ul>
                                        <div class="clear"></div>   
                                    </div>
                                    <?php
                                    $query2 = dibi::query("SELECT * FROM [fp_podnik] WHERE [zverejnit] = %s", "ano", "AND [typ] = %s", $_GET[ad], "ORDER BY %by", $_GET[order], $_GET[by]);
                                    $i = 1;
                                    if ($query2->count() == 0)
                                        echo "<div class=\"msg err\"><h2>Nejsou žádné podniky!</h2><p></p></div>";
                                    while ($row2 = $query2->fetch()) {
                                        $query3 = dibi::query("SELECT * FROM [fp_podnik] WHERE [id] = %i", $row2[id_podnik], "AND [zverejnit] = %s", "ano");
                                        $row3 = $query3->fetch();
                                        ?>
                                        <div id="<?php echo $i; ?>" class="card_prehledy70 border_bottom margin_top3">
                                            <div class="card_vnitrni">
                                                <div>
                                                    <a href="./index.php?id=vyhledavani&ad=<?php echo $row2[typ]; ?>&bd=profil&cd=<?php echo $row2[id]; ?>">
                                                        <div class="grid_1 card_prehledy_border70_bez_cary">
                                                            <div class="card_vertical_align20 ">
                                                                <span class="card_text_cislo_hodnoceni cerna_barva"><?php echo $row2[hodnoceni]; ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="grid_1">
                                                            <div class="card_vertical_align10">
                                                                <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row2[id]); ?>" alt=""/>
                                                            </div>
                                                        </div>
                                                        <div class="grid_4 card_prehledy_border70_bez_cary">
                                                            <div class="card_vertical_align15">
                                                                <span class="card_text_nazev cerna_barva"><?php echo $row2[nazev]; ?></span><br />
                                                                <span class="card_text_procenta "><?php echo $row2[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row2[hodnoceni]); ?>" alt="" height="16px"/></span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="grid_5 ">
                                                        <p class="card_text cerna_barva text_11px"><?php echo substr(nl2br($row2[obsah]), 0, 150); ?>...</a>
                                                        </p>
                                                    </div>
                                                    <div class="grid1">
                                                        <div class="card_vertical_align5">
                                                            <p class="cerna_barva text_11px">
                                                                <?php
                                                                if ($row2[doporucujeme] == "ano") {
                                                                    ?>
                                                                    <a href="administrace.php?id=podniky&ad=<?php echo $_GET[ad]; ?>&oddoporucit=<?php echo $row2[id]; ?>" onclick="if (confirm('Skutečně zrušit doporučení podniku?'))
                                                                                                    location.href = './administrace.php?id=podniky&ad=<?php echo $_GET[ad]; ?>&oddoporucit=<?php echo $row2[id]; ?>';
                                                                                                return(false);">zrušit doporučení</a><br />
                                                                       <?php
                                                                   } else {
                                                                       echo "<a class=\"fancybox_iframe_big fancybox.iframe\" href=\"formulare_admin.php?id=doporucit&ad=$row2[id]&sekce=$_GET[ad]\">doporučit</a><br />";
                                                                   }
                                                                   ?>   
                                                                <a class="fancybox_iframe_big fancybox.iframe" href="formulare_admin.php?id=upravit&ad=<?php echo $row2[id]; ?>&sekce=<?php echo $_GET[ad]; ?>">upravit</a>
                                                                | <a href="administrace.php?id=podniky&ad=<?php echo $_GET[ad]; ?>&smazat=<?php echo $row2[id]; ?>" onclick="if (confirm('Skutečně smazat podnik?'))
                                                                                            location.href = './administrace.php?id=podniky&ad=<?php echo $_GET[ad]; ?>&smazat=<?php echo $row2[id]; ?>';
                                                                                        return(false);">smazat</a>
                                                            </p>
                                                        </div> 
                                                    </div> 
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>  
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        } elseif ($_GET[id] == "recenze") {
                            ?>
                            <h1 id="content_logo_nadpis">Recenze</h1>
                            <?php
                            if ($_GET[smazat] != "") {

                                $arr = array('zverejnit' => 'smazat');
                                dibi::query('UPDATE [fp_recenze] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);
                                $query_id_podniku = dibi::query('SELECT * FROM [fp_recenze] WHERE [id] = %i', $_GET[smazat]);
                                $row_id_podniku = $query_id_podniku->fetch();


                                $hodnoceni = 0;
                                $jidlo = 0;
                                $obsluha = 0;
                                $prostredi = 0;
                                $i = 0;
                                $query = dibi::query('SELECT * FROM [fp_recenze] WHERE [id_podnik] = %i', $row_id_podniku[id_podnik], "AND [zverejnit] = %s", "ano");
                                while ($row = $query->fetch()) {
                                    $jidlo += $row[jidlo];
                                    $obsluha += $row[obsluha];
                                    $prostredi += $row[prostredi];
                                    $hodnoceni += $row[hodnoceni];
                                    $i++;
                                }

                                $jidlo_celek = $jidlo / $i;
                                $obsluha_celek = $obsluha / $i;
                                $prostredi_celek = $prostredi / $i;
                                $hodnoceni_celek = $hodnoceni / $i;

                                $arr_pod = array('jidlo' => $jidlo_celek, 'obsluha' => $obsluha_celek, 'prostredi' => $prostredi_celek, 'hodnoceni' => $hodnoceni_celek);
                                dibi::query('UPDATE [fp_podnik] SET', $arr_pod, 'WHERE [id] = %i', $row_id_podniku[id_podnik]);

                                $query_uzivat = dibi::query('SELECT * FROM [fp_uzivatele] WHERE [id] = %i', $row_id_podniku[id_autor]);
                                $row_uzivat = $query_uzivat->fetch();

                                $arr_uzivat = array('hodnost' => $row_uzivat[hodnost] - 1);
                                dibi::query('UPDATE [fp_uzivatele] SET', $arr_uzivat, ' WHERE [id] = %i', $row_id_podniku[id_autor]);

                                $arr_temp = array('id_cil' => $row_id_podniku[id_autor], 'date' => time(), 'obsah' => "<em>Odmítli jsme Vaší recenzi na podnik <u>" . $row_id_podniku[nazevpodnik] . "</u></em>.", 'stav' => "nova");
                                dibi::query('INSERT INTO [fp_zpravy]', $arr_temp);

                                $body = "Dobrý den,\n\n<em>Odmítli jsme Vaší recenzi na podnik <u>" . $row_id_podniku[nazevpodnik] . "</u></em>. \n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                                send_mail_kovar(get_email_uzivatele($row_id_podniku[id_autor]), "Odmítnutá recene na serveru pjhvysocina.cz", $body);


                                echo "<div class=\"msg information\"><h2>Recenze smazána!</h2><p></p></div>";
                            }
                            ?>
                            <div class="wrapper4">
                                <div id="sezazeni">
                                    <ul>
                                        <?php
                                        if ($_GET[ad] == "") {
                                            echo "<li class=\"first\">Recenze:</li><li><a href=\"./administrace.php?id=recenze&ad=restaurace\">Restaurací</a></li><li><a href=\"./administrace.php?id=recenze&ad=kavarny\">Kaváren</a></li>";
                                        } elseif ($_GET[ad] == "restaurace") {
                                            echo "<li class=\"first\">Recenze:</li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=restaurace\">Restaurací</a></li><li><a href=\"./administrace.php?id=recenze&ad=kavarny\">Kaváren</a></li>";
                                        } elseif ($_GET[ad] == "kavarny") {
                                            echo "<li class=\"first\">Recenze:</li><li><a href=\"./administrace.php?id=recenze&ad=restaurace\">Restaurací</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=kavarny\">Kaváren</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>  
                                <?php
                                if ($_GET[ad] != "") {
                                    ?>
                                </div>  
                                <div class="wrapper3">
                                    <div id="sezazeni">
                                        <a name="hodnoceni"></a>
                                        <ul>
                                            <?php
                                            if (($_GET[order] == "nazevpodnik" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                $_GET[order] = "nazevpodnik";
                                                $_GET[by] = "desc";
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=asc\">Názvu podniku <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "nazevpodnik" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=asc\">Hodnocení <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=asc\">Data <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora</a></li>";
                                            } elseif (($_GET[order] == "nazevautor" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=asc\">Autora <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li>";
                                            } elseif (($_GET[order] == "nazevautor" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=date&by=desc\">Data</a></li><li class=\"active\"><a href=\"./administrace.php?id=recenze&ad=$_GET[ad]&order=nazevautor&by=desc\">Autora <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li>";
                                            }
                                            ?>
                                        </ul>
                                        <div class="clear"></div>   
                                    </div>
                                    <?php
                                    $query2 = dibi::query("SELECT * FROM [fp_recenze] WHERE [zverejnit] = %s", "ano", "AND [typ] = %s", $_GET[ad], "ORDER BY %by", $_GET[order], $_GET[by]);
                                    $i = 1;
                                    if ($query2->count() == 0)
                                        echo "<div class=\"msg err\"><h2>Nejsou žádné recenze!</h2><p>Čekáme až se s námi podělí gurmáni o své kulinářské zážitky.</p></div>";
                                    while ($row2 = $query2->fetch()) {
                                        $query3 = dibi::query("SELECT * FROM [fp_podnik] WHERE [id] = %i", $row2[id_podnik], "AND [zverejnit] = %s", "ano");
                                        $row3 = $query3->fetch();
                                        ?>
                                        <div id="<?php echo $i; ?>" class="card_prehledy70 border_bottom margin_top3">
                                            <div class="card_vnitrni">
                                                <div>
                                                    <a href="./index.php?id=vyhledavani&ad=<?php echo $row3[typ]; ?>&bd=profil&cd=<?php echo $row2[id_podnik]; ?>">
                                                        <div class="grid_1 card_prehledy_border70_bez_cary">
                                                            <div class="card_vertical_align20 ">
                                                                <span class="card_text_cislo_hodnoceni cerna_barva"><?php echo $row2[hodnoceni]; ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="grid_1">
                                                            <div class="card_vertical_align10">
                                                                <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row2[id_podnik]); ?>" alt=""/>
                                                            </div>
                                                        </div>
                                                        <div class="grid_4 card_prehledy_border70_bez_cary">
                                                            <div class="card_vertical_align15">
                                                                <span class="card_text_nazev cerna_barva"><?php echo $row2[nazevpodnik]; ?></span><br />
                                                                <span class="card_text_procenta "><?php echo $row3[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row3[hodnoceni]); ?>" alt="" height="16px"/></span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="grid_5 ">
                                                        <div id="a<?php echo $i; ?>" class=""><p class="card_text cerna_barva text_11px"><span class="bold"><a href="./formulare.php?id=gurman&ad=<?php echo $row2[id_autor]; ?>" class="fancybox_iframe_big fancybox.iframe" title="<?php echo $row2[nazevautor]; ?>"><?php echo $row2[nazevautor]; ?> <span class="text_11px">(<?php echo echo_date($row2[date]); ?>)</span></a></span> <?php echo substr(nl2br($row2[obsah]), 0, 100); ?>...<a href="#" class="cerna_barva bold male" onclick="rolovat_recenzi_down_aktuality(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')"><strong>[zobrazit celý text]</strong></a>
                                                            </p></div>
                                                        <div id="b<?php echo $i; ?>" class="hidden"><p class="card_text cerna_barva text_11px"><span class="bold">(<?php echo echo_date($row2[date]); ?>)</span> <?php echo nl2br($row2[obsah]); ?>
                                                                <br /> <a class="cerna_barva bold male" href="#" onclick="rolovat_recenzi_up_aktuality(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')">[zavřít celý text]</a>
                                                            </p></div>
                                                    </div>
                                                    <div class="grid1">
                                                        <div class="card_vertical_align15">
                                                            <p class="cerna_barva text_11px"><a href="administrace.php?id=recenze&ad=<?php echo $_GET[ad]; ?>&smazat=<?php echo $row2[id]; ?>" onclick="if (confirm('Skutečně smazat recenzi?'))
                                                                                        location.href = './administrace.php?id=recenze&ad=<?php echo $_GET[ad]; ?>&smazat=<?php echo $row2[id]; ?>';
                                                                                    return(false);">smazat</a></p>
                                                        </div> 
                                                    </div> 
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>  
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        } elseif ($_GET[id] == "aktuality") {
                            if ($_GET[smazat] != "") {

                                $arr = array('zverejnit' => 'ne');
                                dibi::query('UPDATE [fp_aktuality] SET ', $arr, 'WHERE [id] = %i', $_GET[smazat]);

                                echo "<div class=\"msg information\"><h2>Aktualita smazána!</h2><p></p></div>";
                            }
                            ?> 
                            <h1 id="content_logo_nadpis">Aktuality restaurací</h1>
                            <div class="wrapper">   
                                <div id="sezazeni">
                                    <ul>
                                        <li class="first"><a href="./formulare_admin.php?id=novaaktualita&ad=restaurace" class="fancybox_iframe_big fancybox.iframe">Nová aktualita</a></li>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>   
                                <?php
                                $query = dibi::query("SELECT * FROM [fp_aktuality] WHERE [sekce] = %s", "restaurace", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
                                if ($query->count() == 0)
                                    echo "<div class=\"msg information\"><h2>Nemáme žádné aktuality k restauracím!</h2></div>";
                                $i = 1;
                                while ($row = $query->fetch()) {
                                    ?>
                                    <div id="<?php echo $i; ?>" class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <a href="./index.php?id=vyhledavani&ad=restaurace">

                                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_4 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                                            <span class="card_text_procenta "><?php echo echo_date($row[date]); ?></span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="grid_5 ">
                                                    <div id="a<?php echo $i; ?>" class=""><p class="card_text cerna_barva text_11px"><?php echo substr(nl2br($row[obsah]), 0, 60); ?>...<a href="#" class="cerna_barva bold male" onclick="rolovat_recenzi_down_aktuality(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')"><strong>[zobrazit celý text]</strong></a>
                                                        </p></div>
                                                    <div id="b<?php echo $i; ?>" class="hidden"><p class="card_text cerna_barva text_11px"><?php echo nl2br($row[obsah]); ?>
                                                            <br /> <a class="cerna_barva bold male" href="#" onclick="rolovat_recenzi_up_aktuality(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')">[zavřít celý text]</a>
                                                        </p></div>
                                                </div>                                                
                                                <div class="grid2 ">

                                                    <p class="cerna_barva text_11px"><a href="administrace.php?id=aktuality&smazat=<?php echo $row[id]; ?>" onclick="if (confirm('Skutečně smazat aktualitu?'))
                                                                            location.href = './administrace.php?id=aktuality&smazat=<?php echo $row[id]; ?>';
                                                                        return(false);">smazat</a><br /><a href="./index.php?id=vyhledavani&ad=restaurace&bd=profil&cd=<?php echo $row[id_podnik]; ?>" title="<?php echo get_nazev_podniku($row[id_podnik]); ?>"><?php echo get_nazev_podniku($row[id_podnik]); ?></a></p>
                                                </div> 

                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                 
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>
                            <h1 id="content_logo_nadpis">Aktuality kaváren</h1>
                            <div class="wrapper">  
                                <div id="sezazeni">
                                    <ul>
                                        <li class="first"><a href="./formulare_admin.php?id=novaaktualita&ad=kavarny" class="fancybox_iframe_big fancybox.iframe">Nová aktualita</a></li>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>   
                                <?php
                                $query = dibi::query("SELECT * FROM [fp_aktuality] WHERE [sekce] = %s", "kavarny", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
                                if ($query->count() == 0)
                                    echo "<div class=\"msg information\"><h2>Nemáme žádné aktuality ke kavárnám!</h2></div>";
                                $i = 1;
                                while ($row = $query->fetch()) {
                                    ?>
                                    <div id="k<?php echo $i; ?>" class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <a href="./index.php?id=vyhledavani&ad=kavarny">
                                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_4 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                                            <span class="card_text_procenta "><?php echo echo_date($row[date]); ?></span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="grid_5 ">
                                                    <div id="ka<?php echo $i; ?>" class=""><p class="card_text cerna_barva text_11px"><?php echo substr(nl2br($row[obsah]), 0, 60); ?>...<a href="#" class="cerna_barva bold male" onclick="rolovat_recenzi_down_aktuality('k<?php echo $i; ?>', 'ka<?php echo $i; ?>', 'kb<?php echo $i; ?>')"><strong>[zobrazit celý text]</strong></a>
                                                        </p></div>
                                                    <div id="kb<?php echo $i; ?>" class="hidden"><p class="card_text cerna_barva text_11px"><?php echo nl2br($row[obsah]); ?>
                                                            <br /> <a class="cerna_barva bold male" href="#" onclick="rolovat_recenzi_up_aktuality('k<?php echo $i; ?>', 'ka<?php echo $i; ?>', 'kb<?php echo $i; ?>')">[zavřít celý text]</a>
                                                        </p></div>
                                                </div>                                                
                                                <div class="grid2 ">
                                                    <p class="cerna_barva text_11px"><a href="administrace.php?id=aktuality&smazat=<?php echo $row[id]; ?>" onclick="if (confirm('Skutečně smazat aktualitu?'))
                                                                            location.href = './administrace.php?id=aktuality&smazat=<?php echo $row[id]; ?>';
                                                                        return(false);">smazat</a><br /><a href="./index.php?id=vyhledavani&ad=kavarny&bd=profil&cd=<?php echo $row[id_podnik]; ?>" title="<?php echo get_nazev_podniku($row[id_podni]); ?>"><?php echo get_nazev_podniku($row[id_podnik]); ?></a></p>
                                                </div> 
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                 
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>                                
                            <?php
                        } elseif ($_GET[id] == "banner") {
                            ?> 
                            <h1 id="content_logo_nadpis">Reklamy</h1>
 <?php
                             if ($_GET[smazat] != "") {

                                $query_maze_foto = dibi::query('SELECT * FROM [fp_bannery] WHERE [id] = %i', $_GET[smazat]);
                                $row_maze_foto = $query_maze_foto->fetch();
                                unlink($row_maze_foto[cil]);
                                dibi::query('DELETE FROM [fp_bannery] WHERE [id] = %i', $row_maze_foto[id]);

                                echo "<div class=\"msg information\"><h2>Reklama byla smazána!</h2><p></p></div>";
                            }
                            if ($_GET[aktivovat] != "") {
                                $arr = array('zverejnit' => 'ano');
                                dibi::query('UPDATE [fp_bannery] SET ', $arr, 'WHERE [id] = %i', $_GET[aktivovat]);
                            
                                echo "<div class=\"msg information\"><h2>Reklama byla aktivována!</h2><p></p></div>";
                            }                            
                            if ($_GET[deaktivovat] != "") {
                                $arr = array('zverejnit' => 'ne');
                                dibi::query('UPDATE [fp_bannery] SET ', $arr, 'WHERE [id] = %i', $_GET[deaktivovat]);
                            
                                echo "<div class=\"msg information\"><h2>Reklama byla deaktivována!</h2><p></p></div>";
                            }                                  
                            
 ?>                           
                            <div class="wrapper">   
                                <div id="sezazeni">
                                    <ul>
                                        <li class="first"><a href="./formulare_admin.php?id=novareklama" class="fancybox_iframe_big fancybox.iframe">Nová reklama</a></li>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>   
                                <?php
                                $query = dibi::query("SELECT * FROM [fp_bannery] ORDER BY %by", "id", "ASC");
                                if ($query->count() == 0)
                                    echo "<div class=\"msg err\"><h2>Nemáme vedené žádné Reklamy!</h2></div>";
                                else {
                                    ?>
                                    <div class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <div class="grid_1 card_prehledy_border70">
                                                    <div class="card_vertical_align15 ">
                                                        <span class="card_text_nazev cerna_barva">stav</span>
                                                    </div>
                                                </div>                                             
                                                <div class="grid_2 card_prehledy_border70">
                                                    <div class="card_vertical_align10">
                                                        <span class="card_text_nazev cerna_barva">cílový podnik</span>
                                                    </div>
                                                </div>
                                                <div class="grid_3 card_prehledy_border70">
                                                    <div class="card_vertical_align10">
                                                        <span class="card_text_nazev cerna_barva">popis</span>
                                                    </div>
                                                </div>                                                   
                                                <div class="grid_2 card_prehledy_border70 ">
                                                    <div class="card_vertical_align10">
                                                        <span class="card_text_nazev cerna_barva">obrázek</span>
                                                    </div>
                                                </div>   
                                                <div class="grid_2 card_prehledy_border70 ">
                                                    <div class="card_vertical_align10">
                                                        <span class="card_text_nazev cerna_barva">zobrazení / kliků</span>
                                                    </div>
                                                </div>                                              
                                                <div class="grid1 ">
                                                    <div class="card_vertical_align10">
                                                        <span class="card_text_nazev cerna_barva">akce</span>
                                                    </div>
                                                </div> 
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                  
                                    <?php
                                    $i = 1;
                                    while ($row = $query->fetch()) {
                                        ?>
                                        <div id="<?php echo $i; ?>" class="card_prehledy70 margin_top3">
                                            <div class="card_vnitrni5">
                                                <div>
                                                    <div class="grid_1 card_prehledy_border70 <?php
                                                    if ($row[zverejnit] == "ano")
                                                        echo "zelena";
                                                    else
                                                        echo "cervena";
                                                    ?>">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_nazev cerna_barva"><?php
                                                                if ($row[zverejnit] == "ano")
                                                                    echo "ZAP";
                                                                else
                                                                    echo "VYP";
                                                                ?></span>
                                                        </div>
                                                    </div>                                                          
                                                    <div class="grid_2 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo get_nazev_podniku($row[id_podnik]) ?></span>
                                                        </div>
                                                    </div>   
                                                    <div class="grid_3 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="grid_2 card_prehledy_border70 ">
                                                        <img class="card_obrazek_podnik80" src="<?php echo $row[cil]; ?>" alt=""/>

                                                    </div>   
                                                    <div class="grid_2 card_prehledy_border70 ">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[zobrazeni]; ?> / <?php echo $row[kliku]; ?></span>
                                                        </div>
                                                    </div>                                                
                                                    <div class="grid1 ">
                                                        <p class="cerna_barva">
                                                                                                                                              <?php
                                                                                  if ($row[zverejnit] == "ano") {
                                                                                      echo "<a href=\"administrace.php?id=banner&deaktivovat=$row[id]\">vypnout</a>";
                                                                                  } else {
                                                                                      echo "<a href=\"administrace.php?id=banner&aktivovat=$row[id]\">zapnout</a>";
                                                                                  }
                                                                                  ?>
                                                            <br /><a href="administrace.php?id=banner&smazat=<?php echo $row[id]; ?>" onclick="if (confirm('Skutečně smazat reklamu?'))
                                                                                    location.href = './administrace.php?id=banner&smazat=<?php echo $row[id]; ?>';
                                                                                return(false);">smazat</a>

                                                        </p>
                                                    </div> 
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>                                                                   
                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </div>                             
                            <?php
                        } elseif ($_GET[id] == "stranky") {
                            ?>
                            <h1 id="content_logo_nadpis">Stránky</h1>
                            <div class="wrapper">
                                <div id="sezazeni">
                                    <ul>
                                        <?php
                                        if ($_GET[ad] == "")
                                            $_GET[ad] = "pravidlahodnoceni";

                                        if ($_GET[ad] == "pravidlahodnoceni") {
                                            echo "<li class=\"first active\"><a href=\"./administrace.php?id=stranky&ad=pravidlahodnoceni\">Pravidla hodnocení</a></li><li><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakty</a></li><li><a href=\"./administrace.php?id=stranky&ad=popis_restaurace\">Popis restaurace</a><li><a href=\"./administrace.php?id=stranky&ad=popis_kavarny\">Popis kavárny</a><li><a href=\"./administrace.php?id=stranky&ad=popis_rychla_obcerstveni\">Popis rychlá obcerstvení</a>";
                                        } elseif ($_GET[ad] == "kontakt") {
                                            echo "<li class=\"first\"><a href=\"./administrace.php?id=stranky&ad=pravidlahodnoceni\">Pravidla hodnocení</a></li><li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakty</a></li><li><a href=\"./administrace.php?id=stranky&ad=popis_restaurace\">Popis restaurace</a><li><a href=\"./administrace.php?id=stranky&ad=popis_kavarny\">Popis kavárny</a><li><a href=\"./administrace.php?id=stranky&ad=popis_rychla_obcerstveni\">Popis rychlá obcerstvení</a>";
                                        } elseif ($_GET[ad] == "popis_restaurace") {
                                            echo "<li class=\"first\"><a href=\"./administrace.php?id=stranky&ad=pravidlahodnoceni\">Pravidla hodnocení</a></li><li><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakty</a></li><li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=popis_restaurace\">Popis restaurace</a><li><a href=\"./administrace.php?id=stranky&ad=popis_kavarny\">Popis kavárny</a><li><a href=\"./administrace.php?id=stranky&ad=popis_rychla_obcerstveni\">Popis rychlá obcerstvení</a>";
                                        } elseif ($_GET[ad] == "popis_kavarny") {
                                            echo "<li class=\"first\"><a href=\"./administrace.php?id=stranky&ad=pravidlahodnoceni\">Pravidla hodnocení</a></li><li><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakty</a></li><li><a href=\"./administrace.php?id=stranky&ad=popis_restaurace\">Popis restaurace</a><li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=popis_kavarny\">Popis kavárny</a><li><a href=\"./administrace.php?id=stranky&ad=popis_rychla_obcerstveni\">Popis rychlá obcerstvení</a>";
                                        } elseif ($_GET[ad] == "popis_rychla_obcerstveni") {
                                            echo "<li class=\"first\"><a href=\"./administrace.php?id=stranky&ad=pravidlahodnoceni\">Pravidla hodnocení</a></li><li><a href=\"./administrace.php?id=stranky&ad=kontakt\">Kontakty</a></li><li><a href=\"./administrace.php?id=stranky&ad=popis_restaurace\">Popis restaurace</a><li><a href=\"./administrace.php?id=stranky&ad=popis_kavarny\">Popis kavárny</a><li class=\"active\"><a href=\"./administrace.php?id=stranky&ad=popis_rychla_obcerstveni\">Popis rychlá obcerstvení</a>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="clear"></div>   
                                </div>
                            </div>    

                            <?php
                            if ($_GET[send] == "ano") {

                                if ($_GET[ad] == "pravidlahodnoceni" || $_GET[ad] == "kontakt") {
                                    $arr = array('obsah' => $_POST[textarea]);
                                    dibi::query('UPDATE [fp_stranky] SET ', $arr, 'WHERE [sekce] = %s', $_GET[ad]);
                                } else {
                                    $arr = array($_GET[ad] => $_POST[textarea]);
                                    dibi::query('UPDATE [fp_global] SET ', $arr, 'WHERE [id] = %i', 1);
                                }
                            }

                            if ($_GET[ad] == "pravidlahodnoceni" || $_GET[ad] == "kontakt") {
                                $query = dibi::query('SELECT * FROM [fp_stranky] WHERE [sekce] = %s', $_GET[ad]);
                                $row = $query->fetch();
                                $obsah = $row[obsah];
                            } else {
                                $query = dibi::query('SELECT * FROM [fp_global] WHERE [id] = %i', 1);
                                $row = $query->fetch();
                                $obsah = $row[$_GET[ad]];
                            }
                            ?>

                            <div id="okno_form">    
                                <form id="formul" name="formul" method="post" action="./administrace.php?id=stranky&ad=<?php echo $_GET[ad]; ?>&send=ano" >
                                    <dl>
                                        <dt><label for="textarea">Obsah</label></dt>
                                        <dd><textarea id="textarea" name="textarea" required><?php echo $obsah; ?></textarea></dd>
                                        <dt></dt>
                                        <dd><input type="submit" class="button" id="text-input-1-submit" value="Upravit">
                                    </dl>
                                </form>  
                            </div>>


                            <?php
                        }
                    } else {
                        ?>
                        <div class="msg err"><h2>Nemáš tu co dělat! Nejsi admin!</h2><p>Jdi na stránky <a href="./index.php">Pijem, jíme, hodnotíme</a>!</p></div>
                        <?php
                    }
                    ?>

                </div>
                <div id="footer">
                    <div class="wrapper_admin">
                        <div class="left">
                            Copyright © 2014 - <a href="http://ldekonom.cz/">Programování a grafický design LDEkonom.cz</a>
                        </div>
                        <div class="right">
                            <ul>
                                <li <?php echo $linksactive[home]; ?>><a href="./administrace.php?id=">Home</a></li>
                                <li <?php echo $linksactive[gurmani]; ?>><a href="./administrace.php?id=gurmani">Gurmáni</a></li>
                                <li <?php echo $linksactive[podniky]; ?>><a href="./administrace.php?id=podniky">Podniky</a></li>
                                <li <?php echo $linksactive[recenze]; ?>><a href="./administrace.php?id=recenze">Recenze</a></li>
                            <!--    <li <?php echo $linksactive[banner]; ?>><a href="./administrace.php?id=banner">Reklama</a></li> -->
                                <li <?php echo $linksactive[aktuality]; ?>><a href="./administrace.php?id=aktuality">Aktuality</a></li>
                                <li <?php echo $linksactive[stranky]; ?>><a href="./administrace.php?id=stranky">Stránky</a></li>     
                                <li><a id="scroll-to-top" href="#top">Zpátky nahoru &raquo;</a></li>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
