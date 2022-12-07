<?php

namespace mydb;

use function GuzzleHttp\Psr7\str;

class myDB{
    private $mysql_host;
    private $mysql_user;
    private $mysql_password;
    private $mysql_database;
    public function __construct($env)
    {
        $this->mysql_host = $env::$DB_HOST;
        $this->mysql_user  = $env::$DB_USERNAME;
        $this->mysql_password = $env::$DB_PASSWORD;
        $this->mysql_database = $env::$DB_DATABASE;
    }
    public function connect(){
        $connect=mysqli_connect($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        mysqli_set_charset($connect, "utf8mb4");
        if ($connect->connect_error) {
            die("Connection failed: " . $connect->connect_error);
        }
        return $connect;
    }
    public function get_last_order(){
        $sql = "SELECT * FROM `test` WHERE 1";
        $result = $this->connect()->query($sql);
        return mysqli_fetch_all($result);
    }
    public function set_last_order($order){
        $sql = "UPDATE `table` SET `table`='".$order."' WHERE 1";
        $result = $this->connect()->query($sql);
        return ($result);
    }
    public function get_task_table($table_name){
        $table_name = "table_".$table_name;
        $sql = "SELECT * FROM `user_task_table` WHERE `user_id`='{$table_name}'";
        $result = $this->connect()->query($sql);
        return mysqli_fetch_all($result)[0];
    }
    public function create_table($table_name){
        $table_name = "table_".$table_name;
        $sql = "CREATE TABLE ".$table_name."(
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            item1 VARCHAR(30) NOT NULL,
            item2 VARCHAR(30) NOT NULL,
            item3 VARCHAR(70) NOT NULL UNIQUE,
            start BOOLEAN
        )";
        if($this->connect()->query($sql)){
            echo "Table created successfully.";
        }else{
            echo "ERROR: Could not able to execute $sql. ";
        }
    }
    public function delete_table($table_name){
        $table_name = "table_".$table_name;
        $delete= $this->connect()->query("DROP TABLE ".$table_name);

        if($delete !== FALSE)
        {
            echo("This table has been deleted.");
        }else{
            echo("This table has not been deleted.");
        }
    }
/*    public function set_task_table($table_name, $item, $str){
        $table_name = "table_".$table_name;
        $sql = "UPDATE ".$table_name." SET ".$item."='".$str."'";
        $result = $this->connect()->query($sql);
        return ($result);
    }*/
    public function create_task_table($table_name){
        $table_name = "table_".$table_name;
        $sql = "INSERT INTO `user_task_table`(`user_id`) VALUES(\"".$table_name."\")";
        $result = $this->connect()->query($sql);
        if($result){
            echo "Table created successfully.";
        }else{
            echo "ERROR: Could not able to execute $sql. ";
        }
        return ($result);
    }
    public function set_task_table($table_name, $item, $val){
        $table_name = "table_".$table_name;
        $sql = "UPDATE `user_task_table` SET `{$item}`='{$val}' WHERE `user_id`='{$table_name}'";
        $result = $this->connect()->query($sql);
        return ($result);
    }
}

