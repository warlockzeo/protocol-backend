<?php
    include_once('./config/cors.php');
    include_once('./config/ClassConection.php');
    require "../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $login = '';
    $senha = '';

    $databaseService = new ClassConection();
    $conn = $databaseService->getConnection();

    $data = json_decode(file_get_contents("php://input"));

    $login = $data->login;
    $senha = $data->senha;

    $table_name = 'users';

    $query = "SELECT * FROM " . $table_name . " WHERE login = ? LIMIT 0,1";

    $stmt = $conn->prepare( $query );
    $stmt->bindParam(1, $login);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $reg = $row['reg'];
        $nome = $row['nome'];
        $nivel = $row['nivel'];
        $senha2 = $row['senha'];
        
        if(password_verify($senha, $senha2)) {
            $secret_key = "YOUR_SECRET_KEY";
            $issuer_claim = "THE_ISSUER";
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = 1356999524; // issued at
            $notbefore_claim = 1357000000; //not before
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "data" => array(
                    "reg" => $reg,
                    "nome" => $nome,
                    "nivel" => $nivel,
                    "login" => $login
            ));
    
            http_response_code(200);
    
            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "expireAt" => "1day"
                )
            );
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login failed.", "senha" => $senha, "senha2" => $senha2));
        }
    }
?>


