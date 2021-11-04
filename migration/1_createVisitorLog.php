<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-11-04 09:24:23
 * @modify date 2021-11-04 09:24:23
 * @desc [description]
 */

class createVisitorLog extends \SLiMS\Migration\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $SQL = "CREATE TABLE `vistor_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `uniqueuserid` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `activity` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `input` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `querystring` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        \SLiMS\DB::getInstance()->query($SQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // \SLiMS\DB::getInstance()->query($SQL);
    }
}