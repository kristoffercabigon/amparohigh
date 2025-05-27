<?php

class connec
{
    public $username = "root";
    public $password = "";
    public $server_name = "localhost";
    public $db_name = "amparohigh";

    public $conn;

    function __construct()
    {
        $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            die("Connection Failed: " . $this->conn->connect_error);
        }
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function select_by_query($query)
    {
        $result = $this->conn->query($query);
        return $result;
    }

    public function insert_lastid($query)
    {
        $last_id;
        if ($this->conn->query($query) === TRUE) {
            $last_id = $this->conn->insert_id;
        } else {
            echo '<script> alert("' . $this->conn->error . '");</script>';
        }
        return $last_id;
    }

    public function insert($query, $msg)
    {
        if ($this->conn->query($query) === TRUE) {
            echo '<script> alert("' . $msg . '");</script>';
        } else {
            echo '<script> alert("' . $this->conn->error . '");</script>';
        }
    }

    public function insert_lastid1($query, $msg)
    {
        $last_id;
        if ($this->conn->query($query) === TRUE) {
            $last_id = $this->conn->insert_id;
            echo '<script> alert("' . $msg . '");</script>';
        } else {
            echo '<script> alert("' . $this->conn->error . '");</script>';
        }
        return $last_id;
    }

    function insert2($query)
    { 
        if($this->conn->query($query) === TRUE)
        {
           
        }
        else
        {
          
        }
    }

    function update1($query)
    {
        $result=$this->conn->query($query);
        return $result;
    }

    function delete($table_name,$id)
    { 
        $query="Delete from $table_name WHERE id =$id";
        if($this->conn->query($query)===TRUE)
        {
             echo '<script> alert("Record Removed");</script>' ;
        }
        else
        {
             echo '<script> alert("'.$this->conn->error.'");</script>' ;
        }
    }

    public function select_login($table_name, $reference_no)
    {
        $sql = "SELECT * FROM $table_name WHERE reference_no='$reference_no'";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function select_login1($table_name, $reg_email)
    {
        $sql = "SELECT * FROM $table_name WHERE reg_email = '$reg_email'";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function real_escape_string($string) {
        return $this->conn->real_escape_string($string);
    }
}
?>
