<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-type: application/json");
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");

    require_once('./config/ClassConection.php');
    
    class ClassProtocol extends ClassConection {
        public function addProtocol($data){
            $date = date("Y/m/d - H:i:s");
            $newProtocolNumber = strtotime("now");

            $origem = $data -> origem;
            $dep_origem = $data -> origemDepartamento;
            $destino = $data -> destino;
            $dep_destino = $data -> destinoDepartamento;

            if(isset($data -> copia)){
                $copia = implode(",", $data -> copia);
            } else {
                $copia = "";
            }

            $portador = $data -> portadorNome;
            $matricula = $data -> portadorMatricula;
            
            if ($data -> carater === "outros") {
                $carater = $data -> caraterOutros;
            } else {
                $carater = $data -> carater;
            }

            if(isset($data -> prazo)){
                $d = new DateTime($data -> prazo);
                $prazo = $d -> format('Y-m-d');
            } else {
                $prazo = "";
            }
            $prazoCampo = $prazo ? "prazo = :prazo,": "";

            if(isset($data -> documento)){
                $doc = implode(",", $data -> documento);
            } else {
                $doc = "";
            }

            $obs = $data -> obs;

            $query = "INSERT INTO protocolo SET data = :data, protocolo = :protocolo, origem = :origem, dep_origem = :dep_origem, destino = :destino, dep_destino = :dep_destino, copia = :copia, portador = :portador, mat = :matricula, situacao = 'Em trânsito', carater = :carater, $prazoCampo doc = :doc, obs = :obs, ver = 1";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':data', $date);
            $stmt->bindParam(':protocolo', $newProtocolNumber);
            $stmt->bindParam(':origem', $origem);
            $stmt->bindParam(':dep_origem', $dep_origem);
            $stmt->bindParam(':destino', $destino);
            $stmt->bindParam(':dep_destino', $dep_destino);
            $stmt->bindParam(':copia', $copia);
            $stmt->bindParam(':portador', $portador);
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':carater', $carater);
            if(!!$prazo){$stmt->bindParam(':prazo', $prazo);}
            $stmt->bindParam(':doc', $doc);
            $stmt->bindParam(':obs', $obs);

            if($stmt -> execute()){
                if(isset($data -> copia)){
                    foreach ($data -> copia as $destino_copia) { 
                        $query2 = "INSERT INTO protocolo SET data = :data, protocolo = :protocolo, origem = :origem, dep_origem = :dep_origem, destino = :destino, copia = 'copia', portador = :portador, mat = :matricula, situacao = 'Em trânsito', carater = :carater, $prazoCampo doc = :doc, obs = :obs, ver = 1";

                        $stmt2 = $this -> getConnection() -> prepare( $query2 );
                        $stmt2->bindParam(':data', $date);
                        $stmt2->bindParam(':protocolo', $newProtocolNumber);
                        $stmt2->bindParam(':origem', $origem);
                        $stmt2->bindParam(':dep_origem', $dep_origem);
                        $stmt2->bindParam(':destino', $destino_copia);
                        $stmt2->bindParam(':portador', $portador);
                        $stmt2->bindParam(':matricula', $matricula);
                        $stmt2->bindParam(':carater', $carater);
                        if(!!$prazo){$stmt2->bindParam(':prazo', $prazo);}
                        $stmt2->bindParam(':doc', $doc);
                        $stmt2->bindParam(':obs', $obs);
                        if(!$stmt2 -> execute()){
                            echo json_encode(["message" => "Unable to register the protocol copia", "sql" => $query2]);
                            print_r($stmt2->errorInfo());
                        }
                    }
                }
                http_response_code(200);
                echo json_encode(["protocolo" => $newProtocolNumber]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the protocol", "sql" => $query]);
                print_r($stmt->errorInfo());
            }
        }

        public function searchProtocol ($data) {
            $search = $data -> search;
            $query = "SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo 
            LEFT JOIN users as origem ON protocolo.origem=origem.reg 
            LEFT JOIN users as destino ON protocolo.destino=destino.reg  WHERE protocolo = :search ORDER BY reg";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':search', $search);
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
                echo json_encode(["message" => "Protocolo $protocolo não encontrado encontrado."]);
            }
        }

        public function listProtocol ($data) {
            $id = $data -> id;
            $situacao = $data -> situacao;
            $copia = $data -> copia;
            $copiaField = ($copia === "copia") ? " AND copia = '" . $copia . "'" : "";
            $carater = $data -> carater;
            $caraterField = ($carater != "") ? " AND carater = '" . $carater . "'" : "";
            $where = "WHERE destino = :id  AND situacao = '$situacao' AND ver = 1 $copiaField $caraterField";
            $query = "SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo 
            LEFT JOIN users as origem ON protocolo.origem=origem.reg 
            LEFT JOIN users as destino ON protocolo.destino=destino.reg $where ORDER BY protocolo DESC";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':id', $id);
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
                echo json_encode(["message" => "Nenhum protocolo encontrado."]);
            }
        }
    }

    $protocol = new ClassProtocol();
    require_once('utils/validateData.php');
    $data = json_decode(file_get_contents("php://input"));
    validateData($data);

    switch($_SERVER['REQUEST_METHOD']){
    case "POST":
        $options = $data -> option;
        switch($options){
        case "search":
            $protocol->searchProtocol($data -> body);
            break;
        case "list":
            $protocol->listProtocol($data -> body);
            break;
        default:
            $protocol->addProtocol($data -> body);
        }
    }

?>