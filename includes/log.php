<?php 
require_once("../includes/dbh.php");

class Log{

    public $log;
    public function newLog($log_text)
    {
        $log = $conn->prepare("INSERT INTO `log`(`log_text`, `created_at`) VALUES (:text,CURRENT_DATE());");
        $log->bindParam(":text",$log_text);
        $log->execute();
    }

    public function deleteLog($log_id)
    {
        $log = $conn->prepare("DELETE FROM `log` WHERE `log_id` = :id");
        $log->bindParam(":id",$log_id);
        $log->execute();
    }
}