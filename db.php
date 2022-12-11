<?php

namespace mydb;

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
    public function get_task_table($from_id){
        $sql = "SELECT * FROM `user_task_table` WHERE `from_id`='{$from_id}' ORDER BY `id` DESC";
        $result = $this->connect()->query($sql);
        return mysqli_fetch_all($result)[0];
    }
    public function create_table($from_id){
        $sql = "CREATE TABLE ".$from_id."(
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
    public function delete_task($id){
        $delete= $this->connect()->query("DELETE FROM `user_task_table` WHERE `id`=".$id);

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
    public function create_task_table($from_id){
        $sql = "INSERT INTO `user_task_table`(`from_id`) VALUES(\"".$from_id."\")";
        $result = $this->connect()->query($sql);
        if($result){
            echo "Table created successfully.";
        }else{
            echo "ERROR: Could not able to execute $sql. ";
        }
        return ($result);
    }
    public function set_task_table($from_id, $item, $val){
        $sql = "UPDATE `user_task_table` SET `{$item}`='{$val}' WHERE `from_id`='{$from_id}'";
        $result = $this->connect()->query($sql);
        return ($result);
    }

    public function set_task_table2($id, $item, $val){
        $sql = "UPDATE `user_task_table` SET `{$item}`='{$val}' WHERE `id`='{$id}'";
        $result = $this->connect()->query($sql);
        return ($result);
    }
}

