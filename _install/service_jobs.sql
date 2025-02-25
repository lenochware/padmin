/*!40101 SET NAMES utf8 */;

/*Data for the table `jobs` */

insert  into `jobs`(`name`,`annotation`,`job_command`,`first_run_at`,`period`,`last_run_at`,`last_run_result`,`last_run_duration`,`active`,`created_at`,`author_id`) values ('fix-default-password','Vymaže plain-text heslo z databáze u všech uživatelů (pokud se používá, převede ho na password-hash)','JobFixDefaultPassword','2023-10-05 14:00:00',0,'0000-00-00 00:00:00','','0.00',0,'2023-10-05 14:22:25',1);
insert  into `jobs`(`name`,`annotation`,`job_command`,`first_run_at`,`period`,`last_run_at`,`last_run_result`,`last_run_duration`,`active`,`created_at`,`author_id`) values ('disable-inactive-users','Deaktivuje účty neaktivní více než jeden rok.\r\n','JobDisableInactiveUsers','2023-10-05 14:00:00',0,'0000-00-00 00:00:00','','0.00',0,'2023-10-05 14:23:10',1);
insert  into `jobs`(`name`,`annotation`,`job_command`,`first_run_at`,`period`,`last_run_at`,`last_run_result`,`last_run_duration`,`active`,`created_at`,`author_id`) values ('migrate-hash','Převede hesla hashovaná pomocí md5 na bcrypt-md5. \r\nPřed spuštěním zálohujte tabulku uživatelů. Po migraci přepněte v konfiguraci pclib.auth algo na \'bcrypt-md5\'.','JobMigrateHash','2023-10-05 14:00:00',0,'0000-00-00 00:00:00','','0.00',0,'2023-10-05 14:24:10',1);
