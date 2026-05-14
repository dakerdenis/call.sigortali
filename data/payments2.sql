CREATE TABLE `payments2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  -- Главная категория: xercler / dovriyye
  `category` enum('xercler','dovriyye') NOT NULL,
  
  -- Подкатегория: sigorta, xerc, medaxil, mexaric, transfer
  `subcategory` enum('sigorta','xerc','medaxil','mexaric','transfer') NOT NULL,
  
  -- Тип страховки (только для sigorta): icbari, kasko, tibbi, emlak, elave_mes, yasil_kart, yuk
  `insurance_type` varchar(50) DEFAULT NULL,
  
  -- Кто платил (только для sigorta): sigortali_odedi / musteri_ozu
  `payer_type` enum('sigortali_odedi','musteri_ozu') DEFAULT NULL,
  
  -- Подтип расхода (для xerc): tibbi, icare, kommunal, diger, mobil, emek, dsfm, vergi
  -- Подтип оборота (для medaxil/mexaric/transfer): hesab_elave, musteriden, vesait_negdl, avans
  `subtype` varchar(50) DEFAULT NULL,
  
  `from_account` varchar(255) NOT NULL DEFAULT '',
  `to_account` varchar(255) NOT NULL DEFAULT '',
  `identification` varchar(255) DEFAULT NULL,
  `car_id` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  
  -- Эффект на баланс: -1 расход, +1 доход, 0 нейтрально
  `effect` tinyint NOT NULL DEFAULT 0,
  
  `paydate` date NOT NULL,
  `note` text DEFAULT NULL,
  
  `status` tinyint NOT NULL DEFAULT 1,
  `deletedby` int(11) NOT NULL DEFAULT 0,
  `createdby` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `subcategory` (`subcategory`),
  KEY `from_account` (`from_account`),
  KEY `to_account` (`to_account`),
  KEY `car_id` (`car_id`),
  KEY `paydate` (`paydate`),
  KEY `deletedby` (`deletedby`),
  KEY `effect` (`effect`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;