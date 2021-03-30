<?php
setcookie('ContadorFallos',0,time()+(60*60*24));
setcookie('Mensaje','-',time()+(60*60*24));
$listapalabras = ["ESCRITORIO", "CASA","SECADOR","CACAHUETE","PINTURA","SILLA","ORDENADOR","CAJA","ZANAHORIA","MERLUZA","SALTAMONTES","RINOCERONTE","MONO","BIBLIOTECA","LIBRO","ESCUELA"];

    if(isset($_REQUEST['generarpalabra'])){ 
        reiniciarCookies();       
        $palabra = generarPalabra($listapalabras);
        $palabraoculta = ocultarPalabra($palabra);
    }

$contadorfallos = $_COOKIE['ContadorFallos'];
$mensaje = $_COOKIE['Mensaje'];
$ganar = false;

    if(isset($_REQUEST['enviar'])){
        $letra = strtoupper($_REQUEST['letra']);       
        $palabra = leerfichero("solucionahorcado.txt");
        $palabraoculta = leerfichero("ahorcado.txt");
        $palabraescondida = array_pop($palabraoculta);
        
        $posicion = comprobarLetra($palabra,$letra);
        if(is_array($posicion)){
            addLetra($palabraoculta,$posicion, $letra);  
            $contadorfallos = $_COOKIE['ContadorFallos'];
            $mensaje = $_COOKIE['Mensaje'];
            recargarCookies($mensaje,$contadorfallos);             
        }else{            
            $contadorfallos = $_COOKIE['ContadorFallos']+1;
            $mensaje = $_COOKIE['Mensaje'];
            $mensaje .= "$letra;- ";
            recargarCookies($mensaje,$contadorfallos);
        }   
    }

    function leerfichero($fichero){
        $archivo = fopen($fichero,"r");
        while(!feof($archivo)){
            $palabra[]=fgetc($archivo);
        }
        fclose($archivo);
        return $palabra;
    }
    
    function reiniciarCookies (){
        $mensaje = "-";
        $contadorfallos = 0;
        $_COOKIE['ContadorFallos'] =  $contadorfallos;
        $_COOKIE['Mensaje'] = $mensaje;
        setcookie('ContadorFallos', $contadorfallos, time()+(60*60*24));
        setcookie('Mensaje', $mensaje, time()+(60*60*24));
    }

    function recargarCookies($mensaje, $contadorfallos){
        setcookie('ContadorFallos',$contadorfallos,time()+(60*60*24));
        setcookie('Mensaje',$mensaje,time()+(60*60*24)); 

    }

    function addLetra($palabraoculta,$posicion,$letra){
        foreach ($posicion as $key => $value) {
            $palabraoculta[$value] = $letra;
        }        
        $ahorcado = fopen("ahorcado.txt","w");
        for($i=0; $i<count($palabraoculta); $i++){
            fputs($ahorcado, $palabraoculta[$i]);            
        }       
        fclose($ahorcado);        
    }
    
    function generarPalabra($listapalabras){        
        $num = random_int(1, count($listapalabras)); 
        $palabraelegida = fopen("solucionahorcado.txt","w");
        fputs($palabraelegida, trim($listapalabras[$num-1]));
        fclose($palabraelegida) ; 
        $palabra = leerfichero("solucionahorcado.txt");              
        return $palabra;      
    }
  
    function ocultarPalabra($palabra){
        for($i=0; $i<count($palabra)-1; $i++){
            $palabraoculta[]= "?";
        }
        $ahorcado = fopen("ahorcado.txt","w");
        for($i=0; $i<count($palabraoculta); $i++){
            fputs($ahorcado, $palabraoculta[$i]);
        }
        return $palabraoculta;
    }

    function pintarPalabra(){
        $palabraoculta = leerfichero("ahorcado.txt");
        $output = "";
        for ($i=0; $i<count($palabraoculta)-1; $i++){
            $output .= "<td>__". $palabraoculta[$i] ."__</td>";
        }        
        echo $output;
    }
    
    function comprobarLetra($palabra, $letra){
        foreach ($palabra as $key => $value) {                           
            if($value == $letra){
                $posicion[] = $key;
            }
        }
        if(isset($posicion)){
            return $posicion; 
        }
        return -1;                       
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Ahorcado</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col jumbotron text-center">
                <h1>AHORCADO, adivina la palabra</h1>        
            </div>
        </div>
        <div class="row bg-light py-3 p-2">
            <div class="col">
                <div class="row">
                    <div class="col mt-10">
                        <form action="ahorcado.php" method="post">
                            <input type="submit" class="btn btn-info" value="GENERAR PALABRA" name="generarpalabra" /><br><br>
                        </form>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col">                    
                        <form action="ahorcado.php" method="post">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-2"><label for="letra">Introduce una letra</label></div>
                                <div class="col-4"><input type="text" class="w-100" name="letra" id="letra" /> </div>
                                <div class="col-2"><input type="submit" value="Enviar" name="enviar" /></div>
                                <div class="col-2"></div>
                            </div>
                        </form>
                    </div>        
                </div>        
                <div class="row mt-5 text-center">
                    <div class="col">    
                        <?php
                            if(isset($palabraoculta)){
                                pintarPalabra();   
                            }                                                
                        ?> 
                    </div>
                </div> 
                <div class="row mt-5 text-center">
                    <div class="col">    
                        <?php                            
                            echo "<p>CONTADOR DE FALLOS: ". $contadorfallos ."</p>";
                            echo "<p>LETRAS QUE NO ESTÁN: <span class='badge badge-pill badge-danger'>". $mensaje ."</span> </p>";                    
                        ?>             
                    </div>
                </div>
            </div>
        </div>      
    </div>
</body>
</html>