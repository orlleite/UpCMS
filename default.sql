
CREATE TABLE IF NOT EXISTS `sys_array` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sys_groups` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `switch_ownread` varchar(255) NOT NULL,
  `ownread` varchar(255) NOT NULL,
  `switch_ownwrite` varchar(255) NOT NULL,
  `ownwrite` varchar(255) NOT NULL,
  `switch_anyread` varchar(255) NOT NULL,
  `anyread` varchar(255) NOT NULL,
  `switch_anywrite` varchar(255) NOT NULL,
  `anywrite` varchar(255) NOT NULL,
  `applications` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `edited_by` int(11) NOT NULL,
  `edited_at` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `sys_groups` (`id`, `name`, `switch_ownread`, `ownread`, `switch_ownwrite`, `ownwrite`, `switch_anyread`, `anyread`, `switch_anywrite`, `anywrite`, `applications`, `created_by`, `created_at`, `edited_by`, `edited_at`) VALUES
(1, 'Administrator', '', '#all#', '', '#all#', '', '#all#', '', '#all#', '#all#', 1, '2012-10-17 17:44:03', 1, '2012-10-17 17:44:03');

CREATE TABLE IF NOT EXISTS `sys_options` (
`id` int(10) unsigned NOT NULL,
  `owner` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

INSERT INTO `sys_options` (`id`, `owner`, `name`, `value`, `status`) VALUES
(1, 'upcms', 'front', 'upfront', 'true'),
(2, 'upcms', 'users_system', 'false', ''),
(3, 'upcms', 'app_name', 'UpCMS', 'true'),
(4, 'upcms', 'app_url', 'http://hizzo.com.br/upcms.net/', 'true'),
(5, 'upcms', 'language', 'pt-BR', 'true'),
(6, 'upcms', 'settings', 'false', ''),
(7, 'upcms', 'list_limit', '20', 'true'),
(8, 'upcms', 'default_cache_time', '600', 'true'),
(9, 'upfront', 'animation_level', '2', 'true'),
(10, 'upfront', 'quickedit', 'true', 'true'),
(11, 'upfront', 'minimize_box', 'true', 'true'),
(12, 'upfront', 'list_thumb_size', '100;70', 'true'),
(13, 'upfront', 'auto_show_table_content', 'false', 'true'),
(14, 'upfront', 'multiple_adding', 'true', 'true'),
(15, 'upfront', 'show_up_version', 'true', 'true'),
(17, 'editposition', 'working', 'true', 'true'),
(18, 'editequipe', 'working', 'true', 'true');

CREATE TABLE IF NOT EXISTS `sys_users` (
`id` int(10) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `displayname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ugroup` int(11) NOT NULL,
  `access` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `edited_by` int(11) NOT NULL,
  `edited_at` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `sys_users` (`id`, `username`, `password`, `fullname`, `displayname`, `email`, `image`, `url`, `ugroup`, `access`, `created_by`, `created_at`, `edited_by`, `edited_at`) VALUES
(1, 'admin', 'd8ed7457a3464c783a4485c5173c8adce2210c1a', 'admin', 'admin', '', '', '', 1, '#allowed#', 1, '2012-10-17 17:44:03', 1, '2012-10-17 17:44:03');

--
-- Indexes for table `sys_array`
--
ALTER TABLE `sys_array`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sys_groups`
--
ALTER TABLE `sys_groups`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sys_options`
--
ALTER TABLE `sys_options`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_users`
--
ALTER TABLE `sys_users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `sys_array`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sys_groups`
--
ALTER TABLE `sys_groups`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sys_options`
--
ALTER TABLE `sys_options`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `sys_users`
--
ALTER TABLE `sys_users`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
