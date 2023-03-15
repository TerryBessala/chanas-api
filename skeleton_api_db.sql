-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Ven 22 Octobre 2021 à 12:32
-- Version du serveur :  5.7.35-0ubuntu0.18.04.1
-- Version de PHP :  7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `skeleton_api_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2020_09_03_171644_create_users_table', 1),
(2, '2020_11_25_141907_create_jobs_table', 1),
(3, '2020_11_25_142137_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` int(11) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `grant_type` varchar(255) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `api_sms_url` varchar(255) NOT NULL,
  `api_sso_url` varchar(255) NOT NULL,
  `api_org_url` varchar(255) NOT NULL,
  `app_web_url` varchar(255) NOT NULL,
  `gen_key` varchar(255) NOT NULL,
  `url_shortener_api_url` varchar(255) NOT NULL,
  `url_shortener_api_key` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `settings`
--

INSERT INTO `settings` (`id`, `client_id`, `client_secret`, `grant_type`, `scope`, `api_sms_url`, `api_sso_url`, `api_org_url`, `app_web_url`, `gen_key`, `url_shortener_api_url`, `url_shortener_api_key`, `created_at`, `updated_at`) VALUES
(1, 16, 'kd4nlZiSk0fBf9tnBYbuM620yvrwCqPVg1yY4FYj', 'password', 'openid email profile roles key', 'https://smsvas.com/bulk/public/index.php/api/v1/', 'https://sso-preprod.nexah.net/', 'https://organization-preprod.nexah.net/api/v1/', 'https://sales-preprod.nexah.net', 'ZeBkwLlaWC3N2G7fMCWGrQ==', 'http://nxh-preprod.nexah.net/api/v1/', '9fcb7cb6397a1488551b664ad330cb3ede60155e6508febac63dabe1016608bb3f6df505711bc765', '2020-04-30 13:33:46', '2020-04-30 13:33:46');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sso_user_id` bigint(20) UNSIGNED NOT NULL,
  `api_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `expires_in` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `sso_user_id`, `api_token`, `access_token`, `expires_in`, `created_at`, `updated_at`) VALUES
(1, 9, 'c521bc6e544186e1db3bbb5dd9ddc9dba5e37b57bb74fd1e478581216127d69c01ebc9d8afc11005', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxNiIsImp0aSI6ImM1MjFiYzZlNTQ0MTg2ZTFkYjNiYmI1ZGQ5ZGRjOWRiYTVlMzdiNTdiYjc0ZmQxZTQ3ODU4MTIxNjEyN2Q2OWMwMWViYzlkOGFmYzExMDA1IiwiaWF0IjoxNjM0OTAxNDI3LCJuYmYiOjE2MzQ5MDE0MjcsImV4cCI6MTY2NjQzNzQyNywic3ViIjoiOSIsInNjb3BlcyI6WyJvcGVuaWQiLCJlbWFpbCIsInByb2ZpbGUiLCJyb2xlcyIsImtleSJdfQ.lOmIOyIb6NauJ_nDceF-lqC856wD7b16y6QLR9H-SLekBGGs-uDHaWnUOhvP3zFeob76PTgynyUpJ0OIKcXAckVMJsm8j5GljjkTtAnZcuT1eI1UYA16YTskmXIpePLlDU74Yfur_mOGuzMvE4zQ45d0PsbVywsqlItnQOqAVaNzMhq-K3NSI_X0w9U10yYNFs0kiT-sCwGkweasaFrk5ifSENXh7BAvjQyBlwo7V17QcBdiHX_gYogCjuxC_GLypEVkQvSt3bG60yJ2qsmadVVg2Kg4zzehwBgknhk3OH6mHqJdm-qfrNl5X5Jl1LAMFbNJ0l3sxtT0mmm74zrvvM5sOzNbtRfkGKQh2Gzig2OgvbG10mtontAZLQwS3zZFIwZLdqLwPJLsJOu5wUbHVYppsKqWjkwDyuYG34BQybPmkwl5WzafYwzIfqoarDuvPxujJenRI9jDj16QPLuz1Hwt6dFMFxebfIDvbQw4qYfs0kR_ofsRFKQsR5HD3ua-rTKe12TaSDbZMkSoVbJlibY30rY-T48ouw-Su_K3-Wb-_-WxNInf2u6hU-JIKrBYB5GVvA_ikKQZwviKrtCbdqdEONSj6ouFMUWTlI5irjVUk5-8bjjWradKXT02I4oKUwyTZcDnohcpHZukm1lb5UszN3vsq34x6Hyya6MiDsA', NULL, '2021-10-22 10:17:08', '2021-10-22 10:17:08');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
