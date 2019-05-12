<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 23/3/2019
 * Time: 1:14 μμ
 */

class Auth
{
    function __construct()
    {
        $this->servername = "HOST";
        $this->username = "USERNAME";
        $this->password = "PASSWORD";
        $this->dbname = "DBNAME";
    }

    function Login($username, $password, $url = null){
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            header("Location: http://apps.3gel.network/accesserror.html");
            exit();
        }
        mysqli_set_charset($conn,"utf8");
        $username_secure = mysqli_escape_string($conn, htmlspecialchars($username));
        $password_secure = $password;
        $url_secure = mysqli_escape_string($conn, htmlspecialchars($url));


        $sql = "SELECT * FROM users WHERE UserEmail='$username_secure'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
               if($row['UserPassword'] == md5(sha1($password_secure))) {
                   //Access Granted;
                   if ($row['UserStatus'] == 1) {
                       if ($url_secure != "" || $url_secure != null) {
                           if (strpos($url_secure, 'apps.3gel.network') !== false) {
                               //Have a valid networked url
                               $_SESSION['uid'] = $row['UserEmail'];
                               return "OK";
                               exit();
                           } else {
                               return "ERROR_URL";
                           }
                       } else {
                           $_SESSION['uid'] = $row['UserEmail'];
                           $this->checkPerms($row['UserEmail'], "");
                           return "OK";
                           exit();
                       }
                   }elseif ($row['UserStatus'] == 0){
                       return "LOCKED";
                       exit();
                   }else{
                       return "SYSTEM_LOCK";
                       exit();
                   }
               }else {
                   return "DENIED";
                   exit();
               }
            }
        }else{
            return "DENIED";
            exit();
        }
    }

    function checkPerms($email, $appurl = null){
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            header("Location: http://apps.3gel.network/accesserror.html");
            exit();
        }
        mysqli_set_charset($conn,"utf8");
        $email_secure = mysqli_escape_string($conn, htmlspecialchars($email));
        $url_secure = mysqli_escape_string($conn, htmlspecialchars($appurl));
        if($url_secure != "" || $url_secure != null) {
            if (strpos($url_secure, 'apps.3gel.network') !== false) {
                //Allowed network url
                $sql = "SELECT * FROM perms WHERE UserEmail='$email_secure'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $userperms = $row['UserApps'];
                    }
                    if ($userperms == "all") {
                        //Acces on all apps
                        return "PERM_OK";
                        echo "Here";
                    } elseif ($userperms != "all") {

                        $sql2 = "SELECT * FROM apps WHERE AppURL='$url_secure'";
                        $result2 = $conn->query($sql2);
                        if ($result2->num_rows > 0) {
                            // output data of each row

                            while ($row2 = $result2->fetch_assoc()) {
                                if (strpos($userperms, $row2['ID']) !== false) {
                                    //User have the permission to access this app
                                    return "PERM_OK";
                                    exit();
                                } else {
                                    //User dont have the permission to access this app
                                    return "PERM_NOT";
                                    exit();
                                }
                            }
                        } else {
                            return "ERROR_URL";
                        }
                    }else{
                        return "PERM_NOT";


                    }
                } else {
                    return "PERM_NOT";

                }

            } else {
                return "ERROR_URL";

            }
        }else{
            return "PERM_NOT_INCLUDE";
        }
    }

    function checkStatus($appid)
    {
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            header("Location: http://apps.3gel.network/accesserror.html");
            exit();
        }
        mysqli_set_charset($conn, "utf8");
        $appid_secure = mysqli_escape_string($conn, htmlspecialchars($appid));


        $sql = "SELECT * FROM apps WHERE ID='$appid_secure'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                if($row['AppStatus'] == 3 || $row['AppStatus'] == 2 || $row['AppView'] == "no"){
                    return "NOT_OK";
                }else{
                    return "OK";
                }
            }
        }
    }
}
