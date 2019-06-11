--This Script creates the needed database for the register script.

CREATE TABLE `user` (
  `user_id` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `rank` int(1) NOT NULL,
  `validation_key` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `register_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `register_date` datetime NOT NULL,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
