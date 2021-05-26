<?php
session_start();
include "koneksi.php";

if (isset($_COOKIE['name']) && isset($_COOKIE['id'])) {
    $id = $_COOKIE['id'];
    $key = $_COOKIE['name'];

    $result = mysqli_query($conn, "SELECT CONCAT(FirstName, , LastName) FROM user WHERE ID = $id");
    $row = mysqli_fetch_assoc($result);

    if ($key === hash('ripemd160', $row['FirstName'] . " " . $row['LastName'])) {
        $_SESSION['name'] = $row['FirstName'] . " " . $row['LastName'];
        $_SESSION["login"] = true;
    }
}

if (isset($_SESSION['name'])) {
    header("Location: ../UTS/homes.php");
    exit;
}


if ($_SESSION["code"] === $_POST["kodecaptcha"]) {
    var_dump("Masuk");
    if (isset($_POST['email']) && isset($_POST['password'])) {
        var_dump("Masuk");
        function validate($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $email = htmlentities(validate($_POST['email']), ENT_QUOTES, 'UTF-8');
        $pass = htmlentities(validate($_POST['password']), ENT_QUOTES, 'UTF-8');
        var_dump($email);
        var_dump($pass);
        if (empty($email)) {
            header("Location: login.php");
            exit();
        } else if (empty($pass)) {
            header("Location: login.php");
            exit();
        } else {
            // hashing the password
            // $pass = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "SELECT * FROM user WHERE Email='$email'";

            $result = mysqli_query($conn, $sql);
        
            if (mysqli_num_rows($result) === 1) {
                
                $row = mysqli_fetch_assoc($result);
                var_dump($row);
                if ($row['Email'] === $email){
                             
                    if(password_verify($pass, $row["Password"])){
                        var_dump("masukferivy");
                        //session
                       

                        if (isset($_POST['remember'])) {
                            //cookie
                            setcookie('id', $row['ID'], time() + 60);
                            
                            setcookie('name', hash('ripemd160', $row['FirstName'] . " " . $row['LastName']), time() + 60);
                        }

                        if ($row['Role'] === '1') { // location -> home page user
                            $_SESSION['name'] = $row['FirstName'] . " " . $row['LastName'];
                            $_SESSION["login"] = true;
                            header("Location: ../UTS/homes.php");

                        } 
                        else if ($row['Role'] === '2') { // location -> admin 
                            $_SESSION['name'] = $row['FirstName'] . " " . $row['LastName'];
                            $_SESSION["login"] = true;
                            $_SESSION["admin"] = true;
                            header("Location: admin/admin.php");
                        }
                                        
                        exit();

                    }

                } 
                else {
                    
                    header("Location: login.php");
                    
                    exit();
                }
            } 
            else {
                
                header("Location: login.php?wrongpass");
                
                exit();
            }
        }
    } 
    else {
        
        header("Location: login.php");
        
        exit();
    }
} 
else {
    header("Location: login.php?wrongcap");
}
