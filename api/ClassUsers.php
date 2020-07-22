<?php 
    include_once("ClassConection.php");

    header("Access-Control-Allow-Origin:*");
    header("Content-type: application/json");
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");

    class ClassUsers extends ClassConection{

        public function addUser()
        {
            $json = file_get_contents('php://input');
            $obj = json_decode($json, TRUE);
            $login = isset($_POST['login']) ? $_POST['login'] : null;
            $password = isset($_POST['password']) ? md5("seguranca".$_POST['password']) : null;

            if($login & $password){
                $sql = "INSERT INTO users (login, password) VALUES ('$login', '$password')";
                $BFetch = $this->conectaDB()->prepare($sql);
                $BFetch->execute(); 

                header("HTTP/1.0 201 Created");
                echo '{"resp":"O usuário ' . $login . ' foi cadastrado com sucesso."}';
            } else {
                header("HTTP/1.0 404 Not found");
                echo '{"resp":"Login ou Palavra Passe não informados."}';
            }
        }

        public function deleteUser()
        {
            $json = file_get_contents('php://input');
            $obj = json_decode($json, TRUE);

            $id = $_GET['id'];
         
            $sql = "DELETE FROM users WHERE id = $id";
            $BFetch = $this->conectaDB()->prepare($sql);
            $BFetch->execute(); 

            header("HTTP/1.0 204 No Content");
        }

        public function updatePassword()
        {
            $json = file_get_contents('php://input');
            $obj = json_decode($json, TRUE);

            $id = $_GET['id'];
            $password = md5("seguranca".$obj['password']);
            $timeStamp = date("Y-m-d H:i:s");

            $sql = "UPDATE users SET password='" . $password . "', updatedAt='" . $timeStamp . "' WHERE id = $id";
            $BFetch=$this->conectaDB()->prepare($sql);
            $BFetch->execute();

            header("HTTP/1.0 201 No Content");
        }

        public function checkPassword()
        {
            $json = file_get_contents('php://input');
            $obj = json_decode($json, TRUE);

            $login = $obj['login'];
            $password = $obj['password'];
            if($login & $password){
                $password = md5("segurança".$password);

                $sql = "SELECT * FROM users WHERE login = '$login'";
                $BFetch=$this->conectaDB()->prepare($sql);
                $BFetch->execute();
                if($user=$BFetch->fetch(PDO::FETCH_ASSOC)){    
                    if($password == $user['password']){
                        header("HTTP/1.0 200 OK");
                    } else {
                        header("HTTP/1.0 401 Password not match");
                        echo '{"resp":"Palavra Passe inválida."}';
                    }
                } else {
                    header("HTTP/1.0 403 Not found");
                    echo '{"resp":"Login desconhecido."}';
                }
            } else {
                header("HTTP/1.0 404 Not found");
                echo '{"resp":"Login ou Palavra Passe não informados."}';
            }
        }
    }
  
    $users = new ClassUsers();

    switch($_SERVER['REQUEST_METHOD']){
    case "POST":
        if(isset($_GET['action']) && $_GET['action']=='login'){
            $users->checkPassword();
            break;
        }
        $users->addUser();
        break;
    case "PUT":
        $users->updatePassword();
        break;
    case "DELETE":
        $users->deleteUser();
        break;
    }

?>