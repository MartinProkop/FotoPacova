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
        // titulek
        if ($_GET[id] == null || $_GET[id] == "index") {
            $title = "FOTKY PACOVA";
            $linksactive[index] = " class=\"active\"";
        } elseif ($_GET[id] == "vyhledavani") {
            if ($_GET[ad] == "") {
                $title = "FOTOGRAFIE | FOTKY PACOVA";
            } else {
                $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                $title = $row[id_zaznamu] . " | FOTOGRAFIE | FOTKY PACOVA";
            }
            $linksactive[vyhledavani] = " class=\"active\"";
        } elseif ($_GET[id] == "vyhledavaniaudio") {
            if ($_GET[ad] == "") {
                $title = "AUDIO | FOTKY PACOVA";
            } else {
                $query = dibi::query('SELECT * FROM [pf_databazeaudio] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                $title = $row[id_zaznamu] . " | AUDIO | FOTKY PACOVA";
            }
            $linksactive[vyhledavaniaudio] = " class=\"active\"";
        } elseif ($_GET[id] == "vyhledavanivideo") {
            if ($_GET[ad] == "") {
                $title = "VIDEO | FOTKY PACOVA";
            } else {
                $query = dibi::query('SELECT * FROM [pf_databazevideo] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                $title = $row[id_zaznamu] . " | VIDEO | FOTKY PACOVA";
            }
            $linksactive[vyhledavanivideo] = " class=\"active\"";
        } elseif ($_GET[id] == "vyhledavanimapy") {
            if ($_GET[ad] == "") {
                $title = "MAPY | FOTKY PACOVA";
            } else {
                $query = dibi::query('SELECT * FROM [pf_databazemapy] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                $title = $row[id_zaznamu] . " | MAPY | FOTKY PACOVA";
            }
            $linksactive[vyhledavanimapy] = " class=\"active\"";
        } elseif ($_GET[id] == "kontakt") {
            $title = "KONTAKT | FOTKY PACOVA";
            $linksactive[kontakt] = " class=\"active\"";
        } elseif ($_GET[id] == "oprojektu") {
            $title = "O PROJEKTU | FOTKY PACOVA";
            $linksactive[oprojektu] = " class=\"active\"";
        } elseif ($_GET[id] == "clanky") {
            $linksactive[clanky] = " class=\"active\"";
            if ($_GET[ad] == "") {
                $title = "ČLÁNKY | FOTKY PACOVA";
            } else {
                $query = dibi::query('SELECT * FROM [pf_clanky] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                $title = "$row[nazev] | FOTKY PACOVA";
            }
        }
        ?>

        <title><?php echo $title; ?></title>

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
                    closeBtn: true,
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

        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;signed_in=true"></script>

        <script>
            function showResult(str) {
                if (str.length == 0) {
                    document.getElementById("livesearch").innerHTML = "";
                    document.getElementById("livesearch").style.border = "0px";
                    return;
                }
                if (window.XMLHttpRequest) {
// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {  // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        document.getElementById("livesearch").innerHTML = xmlhttp.responseText;
                        document.getElementById("livesearch").style.border = "1px solid #A5ACB2";
                    }
                }
                xmlhttp.open("GET", "./lib/search_database_foto.php?q=" + str, true);
                xmlhttp.send();
            }
        </script>
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
                if ($_GET[id] == null || $_GET[id] == "index") {
                    ?>
                    <h1>FOTO PACOVA</h1>
                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer commodo ante nec arcu ornare, eu tristique tellus luctus. Phasellus sit amet metus diam. Maecenas placerat urna ac magna vehicula consectetur. Phasellus quis mauris lorem. Vestibulum tincidunt vel dolor imperdiet commodo. Mauris pharetra bibendum purus, id ullamcorper ex aliquam eu. Morbi tortor purus, ullamcorper accumsan metus in, feugiat sagittis lorem. Donec non varius nulla, nec rutrum quam. Suspendisse vehicula nulla nec enim viverra varius. Ut porttitor posuere dapibus. Donec posuere nisl ante, id lobortis enim aliquet at. Duis a tristique justo, vitae aliquam felis. Pellentesque turpis magna, commodo a turpis sed, suscipit mollis quam. Sed posuere eros gravida mollis congue. Duis mattis consequat magna et rutrum. Aenean in bibendum nunc. </p>
                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer commodo ante nec arcu ornare, eu tristique tellus luctus. Phasellus sit amet metus diam. Maecenas placerat urna ac magna vehicula consectetur. Phasellus quis mauris lorem. Vestibulum tincidunt vel dolor imperdiet commodo. Mauris pharetra bibendum purus, id ullamcorper ex aliquam eu. Morbi tortor purus, ullamcorper accumsan metus in, feugiat sagittis lorem. Donec non varius nulla, nec rutrum quam. Suspendisse vehicula nulla nec enim viverra varius. Ut porttitor posuere dapibus. Donec posuere nisl ante, id lobortis enim aliquet at. Duis a tristique justo, vitae aliquam felis. Pellentesque turpis magna, commodo a turpis sed, suscipit mollis quam. Sed posuere eros gravida mollis congue. Duis mattis consequat magna et rutrum. Aenean in bibendum nunc. </p>
                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer commodo ante nec arcu ornare, eu tristique tellus luctus. Phasellus sit amet metus diam. Maecenas placerat urna ac magna vehicula consectetur. Phasellus quis mauris lorem. Vestibulum tincidunt vel dolor imperdiet commodo. Mauris pharetra bibendum purus, id ullamcorper ex aliquam eu. Morbi tortor purus, ullamcorper accumsan metus in, feugiat sagittis lorem. Donec non varius nulla, nec rutrum quam. Suspendisse vehicula nulla nec enim viverra varius. Ut porttitor posuere dapibus. Donec posuere nisl ante, id lobortis enim aliquet at. Duis a tristique justo, vitae aliquam felis. Pellentesque turpis magna, commodo a turpis sed, suscipit mollis quam. Sed posuere eros gravida mollis congue. Duis mattis consequat magna et rutrum. Aenean in bibendum nunc. </p>

                    <?php
                } elseif ($_GET[id] == "vyhledavani") {
                    if ($_GET[ad] == "") {
                        ?>
                        <h1>Fotografie</h1>

                        <div>
                            <div class="grid_4">
                                <div id="filtr">
                                    <form id="formul" name="formul" method="post" action="./index.php?id=vyhledavani">
                                        <input type="text" class="" id="hledej" autocomplete="off" name="hledej" placeholder="Fulltextový vyhledávač" value="" onkeyup="showResult(this.value)">
                                        <div id="livesearch"></div>
                                        <input type="submit" class="" id="submit1" value="Hledat">

                                    </form>

                                    <?php
                                    if ($_POST["hledej"] == "")
                                        $_POST["hledej"] = $_GET["hledej"];
                                    elseif ($_GET["hledej"] == "")
                                        $_GET["hledej"] = $_POST["hledej"];
                                    ?>

                                    <ul id="filtr-panel">
                                        <li><a href="#">SEŘADIT: </a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=id_zaznamu">ID</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=lokalita">Lokalita</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=cislo_popisne">Císlo popisné</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=udalost">Událost</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=autorvydavatel">Autor/Vydavatel</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=datace_1">Datace</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=typmedia">Typ média</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=skladovano">Archiv</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=klicova_slova">Klíčová</a></li>
                                        <li><a href="./index.php?id=vyhledavani&hledej=<?php echo $_POST["hledej"]; ?>&order=popis">Popis</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="grid_8">
                                <div id="filtr-vysledky">

                                    <?php
                                    if ($_POST[hledej] != "")
                                        echo "<h2>Hledaný výraz: \"$_POST[hledej]\"</h2>";

                                    if ($_GET[order] == "")
                                        $_GET[order] = "id_zaznamu";

                                    megasearch($_POST[hledej], "", $_GET[order], "ASC", 1, 2);
                                    ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>


                        <?php
                    } else {
                        $query = dibi::query('SELECT * FROM [pf_databaze] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        get_foto_of_db_foto($row[id], "big");
                        echo "<h1>#" . $row[id_zaznamu] . "</h1>";
                        echo "Přidáno: " . echo_time($row[date_zverejneni]) . "<br />";
                        echo "lokalita: " . echo_lokalita_of_foto_db($row[lokalita]) . "<br />";
                        echo "číslo popisne: " . $row[cislo_popisne] . "<br />";
                        echo "událost: " . $row[udalost] . "<br />";
                        echo "autor/vydavatel: " . echo_vydavatel_of_foto_db($row[autorvydavatel]) . "<br />";
                        echo "datace1: " . echo_datace_of_foto_db($_GET[ad]) . "<br />";

                        echo "typ média: " . echo_typmedia_of_foto_db($row[typmedia]) . "<br />";
                        echo "archiv: " . echo_archiv_of_foto_db($row[skladovano]) . "<br />";
                        echo "gps: " . get_parse_string_of_db_foto($row[gps]) . "<br />";
                        echo "klíčová slova: " . get_parse_string_of_db_foto($row[klicova_slova]) . "<br />";
                        echo "popis: " . nl2br($row[popis]) . "<br />";
                        echo_relations($row[id]);

                        $gpspole = explode(";", $row[gps]);
                        $pocetcount = count($gpspole);

                        //tutaj
                        if ($row[gpsor] == "marker") {
                            ?>
                            <script>
                                function initialize() {
            <?php
            for ($i = 0; $i < $pocetcount; $i++) {
                ?>
                                        var myLatlng<?php echo $i; ?> = new google.maps.LatLng(<?php echo $gpspole[$i]; ?>);
                <?php
            }
            ?>

                                    var mapOptions = {
                                        zoom: 16,
                                        center: myLatlng0,
                                        disableDefaultUI: false,
                                        mapTypeId: google.maps.MapTypeId.SATELLITE,
                                        panControl: true,
                                        zoomControl: true,
                                        mapTypeControl: true,
                                        scaleControl: true,
                                        streetViewControl: true,
                                        overviewMapControl: true

                                    }
                                    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                                    var contentString = '<div id="contentmarket">' +
                                            '<div id="siteNotice">' +
                                            '</div>' +
                                            '<h1 id="Heading" class="Heading">#<?php echo $row[id_zaznamu]; ?></h1>' +
                                            '<div id="bodyContent">' +
                                            '<p><?php echo $row[popis]; ?> ' +
                                            '<p>FOTOPACOVA: <a href="#">fotopacova' +
                                            '</a> ' +
                                            '(treba datum).</p>' +
                                            '</div>' +
                                            '</div>';

                                    var infowindow = new google.maps.InfoWindow({
                                        content: contentString
                                    });

            <?php
            for ($i = 0; $i < $pocetcount; $i++) {
                ?>

                                        var marker<?php echo $i; ?> = new google.maps.Marker({
                                            position: myLatlng<?php echo $i; ?>,
                                            map: map,
                                            title: '<?php echo $row[id_zaznamu]; ?>'
                                        });

                                        google.maps.event.addListener(marker<?php echo $i; ?>, 'click', function () {
                                            infowindow.open(map, marker<?php echo $i; ?>);
                                        });
                <?php
            }
            ?>
                                }
                                google.maps.event.addDomListener(window, 'load', initialize);

                            </script>

                            <div id="map-canvas"></div>
                            <?php
                        } elseif ($row[gpsor] == "polygon") {
                            ?>
                            <script>


                                function initialize() {
            <?php
            for ($i = 0; $i < $pocetcount; $i++) {
                ?>
                                        var myLatlng<?php echo $i; ?> = new google.maps.LatLng(<?php echo $gpspole[$i]; ?>);
                <?php
            }
            ?>
                                    var mapOptions = {
                                        zoom: 16,
                                        center: myLatlng0,
                                        disableDefaultUI: false,
                                        mapTypeId: google.maps.MapTypeId.SATELLITE,
                                        panControl: true,
                                        zoomControl: true,
                                        mapTypeControl: true,
                                        scaleControl: true,
                                        streetViewControl: true,
                                        overviewMapControl: true

                                    }

                                    var map = new google.maps.Map(document.getElementById('map-canvas'),
                                            mapOptions);

                                    var flightPlanCoordinates = [
            <?php
            for ($i = 0; $i < $pocetcount; $i++) {
                ?>
                                            new google.maps.LatLng(<?php echo $gpspole[$i]; ?>),
                <?php
            }
            ?>];
                                    var flightPath = new google.maps.Polyline({
                                        path: flightPlanCoordinates,
                                        geodesic: true,
                                        strokeColor: '#FF0000',
                                        strokeOpacity: 1.0,
                                        strokeWeight: 2
                                    });

                                    flightPath.setMap(map);
                                }

                                google.maps.event.addDomListener(window, 'load', initialize);
                            </script>
                            <div id="map-canvas"></div>
            <?php
        }
        ?>

                        <?php
                        echo "<h2>Komentaře</h2>";
                        echo "<a href=\"./formulare.php?id=novykomentar&ad=$row[id]\" class=\"fancybox_iframe fancybox.iframe\">přidat komentář</a><br />";
                        $query2 = dibi::query('SELECT * FROM [pf_komentare] WHERE [id_fotka] = %i', $row[id], 'AND [zverejnit] = %s', "ano", 'ORDER BY %by', "id", "DESC");
                        while ($row2 = $query2->fetch()) {
                            echo "<div class=\"block\">";
                            get_foto_of_user(get_id_uzivatele($row2[uzivatel]));
                            echo "<a href=\"./formulare.php?id=uzivatel&amp;ad=" . get_id_uzivatele($row2[uzivatel]) . "\" class=\"fancybox_iframe fancybox.iframe\">$row2[uzivatel]</a><br />";
                            echo echo_time($row2[date]) . " | hodnocení: " . $row2[hodnoceni] . "<br />";
                            echo nl2br($row2[obsah]);
                            echo "</div><div class=\"clear\"></div>";
                        }
                    }
                } elseif ($_GET[id] == "vyhledavaniaudio") {
                    ?>
                    <h1>Audio</h1>

                    <?php
                } elseif ($_GET[id] == "vyhledavanivideo") {
                    ?>
                    <h1>Video</h1>

                    <?php
                } elseif ($_GET[id] == "vyhledavanimapy") {
                    ?>
                    <h1>Mapy</h1>

                    <?php
                } elseif ($_GET[id] == "oprojektu") {
                    ?>
                    <h1>O projektu</h1>
                    <?php
                    $query = dibi::query('SELECT * FROM [pf_stranky] WHERE [sekce] = %s', "oprojektu");
                    $row = $query->fetch();
                    echo nl2br($row[obsah]);
                    ?>
                    <?php
                } elseif ($_GET[id] == "kontakt") {
                    ?>
                    <h1>Kontakt</h1>
                    <?php
                    $query = dibi::query('SELECT * FROM [pf_stranky] WHERE [sekce] = %s', "kontakt");
                    $row = $query->fetch();
                    echo nl2br($row[obsah]);
                    ?>
                    <?php
                } elseif ($_GET[id] == "clanky") {
                    ?>

                    <?php
                    if ($_GET[ad] == "") {
                        echo "<h1>Články</h1>";
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
                            echo "</div><div class=\"clear\"></div>";
                        }
                    } else {
                        $query = dibi::query('SELECT * FROM [pf_clanky] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        echo "<div class=\"block\">";
                        get_foto_of_clanek($row[id]);
                        echo "<h1>$row[nazev]</h1>";
                        if ($row[id_relace] != 0) {
                            if ($row[typ] == "fotka")
                                echo "relace na fotografii <a href=\"./index.php?id=vyhledavani&ad=$row[id_relace]\">#" . get_idzaznamu_fotka($row[id_relace]) . "</a><br />";
                        }
                        echo echo_time($row[date]) . "<br />";
                        echo nl2br($row[obsah]);

                        echo "</div><div class=\"clear\"></div>";
                    }
                    ?>

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
