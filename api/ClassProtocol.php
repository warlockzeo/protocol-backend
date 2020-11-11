<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-type: application/json");
    header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");

    require_once('./config/ClassConection.php');
    require_once('utils/validateData.php');
    
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

        public function reSendProtocol($data){
            $date = date("Y/m/d - H:i:s");

            $reg = $data -> reg;
            $protocolo = $data -> protocolo;
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

            $query0 = "UPDATE protocolo SET ver = 0 WHERE reg = $reg";
            $stmt0 = $this -> getConnection() -> prepare( $query0 );
            $stmt0 -> execute();

            $query = "INSERT INTO protocolo SET data = :data, protocolo = :protocolo, origem = :origem, dep_origem = :dep_origem, destino = :destino, dep_destino = :dep_destino, copia = :copia, portador = :portador, mat = :matricula, situacao = 'Em trânsito', carater = :carater, $prazoCampo doc = :doc, obs = :obs, ver = 1";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':data', $date);
            $stmt->bindParam(':protocolo', $protocolo);
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
                        $stmt2->bindParam(':protocolo', $protocolo);
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
                echo json_encode(["protocolo" => $protocolo]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Unable to register the protocol", "sql" => $query]);
                print_r($stmt->errorInfo());
            }
        }

        public function updateProtocolStatus ($data) {
            $reg = $data -> reg;
            $situacao = $data -> situacao;
 
            $query = "UPDATE protocolo SET ver = 0 WHERE reg = $reg";
            $stmt = $this -> getConnection() -> prepare( $query );

            if($stmt -> execute()){
                $query2 = "SELECT * FROM protocolo WHERE reg = $reg";
                $stmt2 = $this -> getConnection() -> prepare( $query2 );
    
                if($stmt2 -> execute()){
                    $row = $stmt2 -> fetch(PDO::FETCH_ASSOC);

                    $date = date("Y/m/d - H:i:s");
                    $query3 = "INSERT INTO protocolo SET data ='$date', protocolo = $row[protocolo], origem = '$row[origem]', dep_origem = '$row[dep_origem]', destino = '$row[destino]', dep_destino = '$row[dep_destino]', copia = '$row[copia]', portador = '$row[portador]', mat = '$row[mat]', situacao = '$situacao', carater = '', doc = '$row[doc]', obs = '$row[obs]', ver = 1";

                    $stmt3 = $this -> getConnection() -> prepare( $query3 );
                    if($stmt3 -> execute()){
                        http_response_code(200);
                        echo json_encode(["message" => "Situação do protocolo $row[protocolo] atualizada"]);
                    } else {
                        http_response_code(400);
                        echo json_encode(["message" => "Unable to register the protocol", "sql" => $query3]);
                        print_r($stmt3->errorInfo());
                    }
                }
            }
        }

        public function searchProtocol ($data) {
            $search = $data -> search;
            $query = "SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo LEFT JOIN users as origem ON protocolo.origem=origem.reg LEFT JOIN users as destino ON protocolo.destino=destino.reg  WHERE protocolo = :search ORDER BY reg";

            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt->bindParam(':search', $search);
            $stmt -> execute();
            $num = $stmt->rowCount();

            if($num >= 1){
                $resp = [];
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                    array_push($resp, $row);
                }
                echo json_encode($resp);
            }  else {
                http_response_code(401);
                echo json_encode(["message" => "Protocolo $search não encontrado encontrado."]);
            }
        }

        public function listProtocol ($data) {
            $origem = $data -> origem;
            $origemField = ($origem != "") ? " origem = '" . $origem . "'" : "";
            $destino = $data -> destino;
            $destinoField = ($destino != "") ? " destino = '" . $destino . "'" : "";
            $situacao = $data -> situacao;
            $copia = $data -> copia;
            $copiaField = ($copia === "copia") ? " AND copia = '" . $copia . "'" : "";
            $carater = $data -> carater;
            $caraterField = ($carater != "") ? " AND carater = '" . $carater . "'" : "";
            $and = ($destino || $origem ? " AND " : "");
            $where = "WHERE $origemField $destinoField  $and situacao = '$situacao' AND ver = 1 $copiaField $caraterField";
            $query = "SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo 
            LEFT JOIN users as origem ON protocolo.origem=origem.reg 
            LEFT JOIN users as destino ON protocolo.destino=destino.reg $where ORDER BY protocolo DESC";

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
                echo json_encode(["message" => "Nenhum protocolo encontrado.", "query" => $query]);
            }
        }

        public function reportProtocol ($data) {
            //modelo de sql funcionando para relatório
            /* SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo 
            LEFT JOIN users as origem ON (protocolo.origem=origem.reg OR protocolo.origem=origem.nome) 
            LEFT JOIN users as destino ON (protocolo.destino=destino.reg OR protocolo.destino=destino.nome) 
            WHERE (origem = 'rogeria' OR destino = 'rogeria' OR origem = '37' OR destino='37') 
            AND data >= '2013-11-01, 00:00:00' AND data <= '2020-10-13, 23:59:00' ORDER BY protocolo, reg
            */

            $where = "";
            $secretaria = $data -> secretaria;
            $secretariaNome = $data -> secretariaNome ? $data -> secretariaNome : "";
            $situacao = $data -> situacao;

            switch($situacao){
                case "Enviados":
                    $where = "(origem = '$secretaria' OR origem = '$secretariaNome')";
                    break;
                case "Recebidos":
                    $where = "(destino = '$secretaria' OR destino = '$secretariaNome')";
                    break;
                case "Todos":
                    $where = $secretaria != "Todos" ? "(origem = '$secretaria' OR origem = '$secretariaNome' OR destino = '$secretaria' OR destino = '$secretariaNome')" : "";
                    break;
                default:
                    $where = $secretaria != "Todos" ? "( $situacaoField ) AND (origem = '$secretaria' OR origem = '$secretariaNome' OR destino = '$secretaria' OR destino = '$secretariaNome')" : "situacao = '$situacao'";
            }
             
            $where .= $where != "" ? " AND " : "";
            $de = $data -> de;
            $where .= "data >= '$de, 00:00:00'";
            $ate = $data -> ate;
            $where .= " AND data <= '$ate, 23:59:00'";

            $query = "SELECT protocolo.*, origem.nome as origemNome, destino.nome as destinoNome FROM protocolo LEFT JOIN users as origem ON protocolo.origem=origem.reg LEFT JOIN users as destino ON protocolo.destino=destino.reg WHERE $where ORDER BY protocolo, reg";
            $stmt = $this -> getConnection() -> prepare( $query );
            $stmt -> execute();
            $num = $stmt->rowCount();

            if($num >= 1){
                $resp = [];
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                    array_push($resp, $row);
                }
                //var_dump($resp);
                $resp = validateData($resp);
                echo json_encode($resp);
            }  else {
                http_response_code(401);
                echo json_encode(["message" => "Nenhum protocolo encontrado.", "sql" => $query]);
            }
        }
    }


    $protocol = new ClassProtocol();
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
        case "update":
            $protocol->updateProtocolStatus($data -> body);
            break;
        case "resend":
            $protocol->reSendProtocol($data -> body);
            break;
        case "report":
            $protocol->reportProtocol($data -> body);
            break;
        default:
            $protocol->addProtocol($data -> body);
        }
    }

?>