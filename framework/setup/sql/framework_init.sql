/*
SQLyog Ultimate v8.32 
MySQL - 5.5.29-log : Database - ycoa23
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `yc_role` */

DROP TABLE IF EXISTS `yc_role`;

CREATE TABLE `yc_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL,
  `role_desc` varchar(255) DEFAULT NULL,
  `role_is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `yc_role` */

insert  into `yc_role`(`role_id`,`role_name`,`role_desc`,`role_is_active`) values (1,'补货员','负责补货',1),(2,'test','ddd',0);

/*Table structure for table `yc_user` */

DROP TABLE IF EXISTS `yc_user`;

CREATE TABLE `yc_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  `password` varchar(50) NOT NULL DEFAULT 'none',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `yc_user` */

insert  into `yc_user`(`user_id`,`user_name`,`user_group_id`,`password`,`status`) values (1,'Guest',1,'none',1),(2,'ardar',1,'6c14da109e294d1e8155be8aa4b1ce8e',1);

/*Table structure for table `yc_user_role` */

DROP TABLE IF EXISTS `yc_user_role`;

CREATE TABLE `yc_user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `yc_user_role` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
