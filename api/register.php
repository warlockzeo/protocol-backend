<?php
include_once('./config/ClassConection.php');
include_once('./config/cors.php');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$nome = '';
$nivel = '';
$login = '';
$senha = '';
$conn = null;

$databaseService = new ClassConection();
$conn = $databaseService->getConnection();

$json = file_get_contents("php://input");
$data = json_decode($json, TRUE);

$login = $data['login'];
$senha = $data['senha'];
$nivel = $data['nivel'];
$nome = $data['nome'];

$table_name = 'users';

$query = "INSERT INTO " . $table_name . "
                SET login = :login,
                    senha = :senha,
                    nivel = :nivel,
                    nome = :nome";

$stmt = $conn->prepare($query);

$stmt->bindParam(':login', $login);
$stmt->bindParam(':nivel', $nivel);
$stmt->bindParam(':nome', $nome);

$password_hash = password_hash($senha, PASSWORD_BCRYPT);

$stmt->bindParam(':senha', $password_hash);

if($stmt->execute()){
    http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register the user"));
    print_r($stmt->errorInfo());
}
?>