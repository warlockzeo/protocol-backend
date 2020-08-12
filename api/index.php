<?php 
    if($_GET['tabela']=='protocolo'){
        include("ClassProtocolos.php");

        $protocolos=new ClassProtocolos();

        if($_GET['opcao']=='listar'){
            $protocolos->listaProtocolos();
        } 
    }

    elseif($_GET['tabela']=='users'){
        include("ClassUsers.php");

        $users=new ClassUsers();

        if($_GET['opcao']=='listar'){
            $users->listaUsers();
        } 
    }
    
?>