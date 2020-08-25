<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-type: application/json");
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");

    require_once('./config/ClassConection.php');
    
    class ClassUsers extends ClassConection {
        public function listUsers(){
            $query = "SELECT reg as id, login, nome, nivel FROM users";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt -> execute();
            $num = $stmt->rowCount();

            if($num > 0) {
                $resp = [];
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){                
                    array_push($resp, $row);
                }
                echo json_encode($resp);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Nenhum usuário encontrado."]);
            }
        }

        public function updateUser($data){
            $id = $data -> id;
            $login = $data -> login;
            $nivel = $data -> nivel;
            $nome = $data -> nome;

            $query = "UPDATE users SET login = :login, nivel = :nivel, nome = :nome WHERE reg = :id";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':nivel', $nivel);
            $stmt->bindParam(':nome', $nome);

            if($stmt -> execute()){
                http_response_code(200);
                echo json_encode(["message" => "User was successfully updated."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to update user"]);
                print_r($stmt->errorInfo());
            }
        }

        public function blockUser($data){
            $id = $data -> id;
            $query = "UPDATE users SET nivel = 0 WHERE reg = :id";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':id', $id);
            if($stmt -> execute()){
                http_response_code(200);
                echo json_encode(["message" => "User was successfully registered."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the user"]);
                print_r($stmt->errorInfo());
            }
        }

        public function unblockUser($data){
            $id = $data -> id;
            $query = "UPDATE users SET nivel = 1 WHERE reg = :id";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':id', $id);
            if($stmt -> execute()){
                http_response_code(200);
                echo json_encode(["message" => "User was successfully registered."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the user"]);
                print_r($stmt->errorInfo());
            }
        }
        
        public function updatePassword($data){
            $id = $data -> id;
            $senha = password_hash($data -> senha, PASSWORD_BCRYPT);
            $query = "UPDATE users SET senha = :senha WHERE reg = :id";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':senha', $senha);
            if($stmt -> execute()){
                http_response_code(200);
                echo json_encode(["message" => "User was successfully registered."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the user"]);
                print_r($stmt->errorInfo());
            }
        }

        public function addUser($data){
            $login = $data -> login;
            $senha = $data -> senha;
            $nivel = $data -> nivel;
            $nome = $data -> nome;

            $query = "INSERT INTO users SET login = :login, senha = :senha, nivel = :nivel, nome = :nome";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':nivel', $nivel);
            $stmt->bindParam(':nome', $nome);
            $password_hash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt->bindParam(':senha', $password_hash);

            if($stmt -> execute()){

                $query2 = "SELECT reg as id, login, nome, nivel FROM users ORDER BY reg DESC LIMIT 1";

                $stmt2 = $this -> getConnection() -> prepare( $query2 );
                $stmt2 -> execute();
                $resp = $stmt2 -> fetch(PDO::FETCH_ASSOC);


                http_response_code(200);
                echo json_encode($resp);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the user"]);
                print_r($stmt->errorInfo());
            }
        }
    }
  
    $users = new ClassUsers();
    require_once('utils/validateData.php');
    $data = json_decode(file_get_contents("php://input"));
    validateData($data);

    switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        $users->listUsers();
        break;
    case "PUT":
        $users->updateUser($data -> body);
        break;
    case "POST":
        $options = $data -> option;
        switch($options){
        case "block":
            $users->blockUser($data -> body);
            break;
        case "unblock":
            $users->unblockUser($data -> body);
            break;
        case "updatePassword":
            $users->updatePassword($data -> body);
            break;
        default:
            $users->addUser($data -> body);
        }
    }

?>