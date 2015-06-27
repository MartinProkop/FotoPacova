-- phpMyAdmin SQL Dump
-- version 4.2.6deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Ned 08. bře 2015, 18:00
-- Verze serveru: 5.5.41-0ubuntu0.14.10.1
-- Verze PHP: 5.5.12-2ubuntu4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `pacovskefotky`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `pf_archiv`
--

CREATE TABLE IF NOT EXISTS `pf_archiv` (
`id` int(20) NOT NULL,
  `archiv` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `pf_komentare_admins`
--

CREATE TABLE IF NOT EXISTS `pf_komentare_admins` (
`id` int(10) NOT NULL,
  `uzivatel` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `id_fotka` int(20) NOT NULL,
  `date` int(50) NOT NULL,
  `obsah` text COLLATE utf8_czech_ci NOT NULL,
  `hodnoceni` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `zverejnit` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `pf_typmedia`
--

CREATE TABLE IF NOT EXISTS `pf_typmedia` (
`id` int(20) NOT NULL,
  `typmedia` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `pf_vydavatel`
--

CREATE TABLE IF NOT EXISTS `pf_vydavatel` (
`id` int(20) NOT NULL,
  `vydavatel` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=34 ;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `pf_archiv`
--
ALTER TABLE `pf_archiv`
 ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `pf_komentare_admins`
--
ALTER TABLE `pf_komentare_admins`
 ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `pf_typmedia`
--
ALTER TABLE `pf_typmedia`
 ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `pf_vydavatel`
--
ALTER TABLE `pf_vydavatel`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `pf_archiv`
--
ALTER TABLE `pf_archiv`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT pro tabulku `pf_komentare_admins`
--
ALTER TABLE `pf_komentare_admins`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `pf_typmedia`
--
ALTER TABLE `pf_typmedia`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT pro tabulku `pf_vydavatel`
--
ALTER TABLE `pf_vydavatel`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
