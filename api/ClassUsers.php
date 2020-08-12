<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-type: application/json");
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");

    include_once('./config/ClassConection.php');
    
    class ClassUsers extends ClassConection {
        public function listUsers(){
            $query = "SELECT reg, login, nome, nivel FROM users";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt -> execute();
            $num = $stmt->rowCount();

            if($num > 0) {
                $resp = [];
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){                
                    array_push($resp, $row);
                }
                echo json_encode($resp);
            }
        }
    }
  
    $users = new ClassUsers();
    $data = json_decode(file_get_contents("php://input"));

    switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        $users->listUsers();
        break;
    case "PUT":
        //$users->updateUser();
        echo "put";
        break;
    case "POST":
        $options = $data -> option;
        switch($options){
        case "block":
            //$users->blockUser();
            echo "block";
            break;
        case "unblock":
            $users->unblockUser();
            break;
        case "updatePassword":
            $users->updatePassword();
            break;
        default:
            //$users->addUser();
            echo "add user";
        }
    }

?>