<?php

require_once(__DIR__ . '/../../autoload.php');

class Rest {

    public $_conn = NULL;

    public function conectarDB() 
    {
        $dsn = 'mysql:dbname=' . env('DB_NAME') . '; host=' . env('DB_HOST') . '; port=' . env('DB_PORT') . '; charset=UTF8';
        try {
            $this->_conn = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'));
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }
    }

    public $tipo = "application/json";
    public $datosPeticion = array();
    private $_codEstado = 200;

    public function __construct() 
    {
        $this->tratarEntrada();
        $this->conectarDB();
        if (!$this->isLoginPage()) {
            $this->token();
        }
    }

    private function convertirJson($data) 
    {
        return json_encode($data);
    }

    public function mostrarRespuesta($data, $estado) 
    {
        $this->_codEstado = ($estado) ? $estado : 200;
        $this->setCabecera();
        echo $this->convertirJson($data);
        exit;
    }

    private function setCabecera() 
    {
        header("HTTP/1.1 " . $this->_codEstado . " " . $this->getCodEstado());
        header("Content-Type:" . $this->tipo . ';charset=utf-8');
    }

    private function limpiarEntrada($data) 
    {
        $entrada = array();
        var_dump($data);
        foreach ($data as $key => $value) {
            $entrada[$key] = $this->limpiarEntrada($value);
        }
        return $entrada;
    }

    private function tratarEntrada() 
    {
        $metodo = $_SERVER['REQUEST_METHOD'];

        switch ($metodo) {
            case "GET":
                $this->datosPeticion = $_GET;
                break;
            case "POST":
                $this->datosPeticion = $_POST;
                break;
            case "DELETE"://"falling though". Se ejecutar� el case siguiente  
            case "PUT":
                //php no tiene un m�todo propiamente dicho para leer una petici�n PUT o DELETE por lo que se usa un "truco":  
                //leer el stream de entrada file_get_contents("php://input") que transfiere un fichero a una cadena.  
                //Con ello obtenemos una cadena de pares clave valor de variables (variable1=dato1&variable2=data2...)
                //que evidentemente tendremos que transformarla a un array asociativo.  
                //Con parse_str meteremos la cadena en un array donde cada par de elementos es un componente del array.  
                parse_str(file_get_contents("php://input"), $this->datosPeticion);
                $this->datosPeticion = $this->limpiarEntrada($this->datosPeticion);
                break;
            default:
                $this->response('', 404);
                break;
        }
    }

    private function getCodEstado() 
    {
        $estado = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error');
        $respuesta = ($estado[$this->_codEstado]) ? $estado[$this->_codEstado] : $estado[500];
        return $respuesta;
    }

    /**
     * GESTIÓN DE TOKEN
     */
    public function generarToken() 
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 20);
    }

    private function verificarToken($UserToken) 
    {
        $strsql = 
            "SELECT 
                CASE 
                    WHEN '" . date("Y-m-d H:i:s") . "' < validity_date 
                        THEN 'si' 
                    ELSE 'no' 
                END AS valido 
                FROM users 
                    WHERE token = '$UserToken'";

        $query = $this->_conn->prepare($strsql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        return is_array($result) && $result['valido'] == 'si' ? 'si' : 'no';
    }

    private function actualizarToken($UserToken) 
    {
        $datetime = date('Y-m-d H:i:s');
        $strsql = 
            "UPDATE users 
                SET validity_date = DATE_ADD('" . $datetime . "', INTERVAL 20 MINUTE) 
                    WHERE token = '$UserToken'";

        $query = $this->_conn->prepare($strsql);
        $query->execute();
    }

    private function token() 
    {
        $UserToken = $_SESSION['token'];
        if ($this->verificarToken($UserToken) === 'no') {
            unset($_SESSION);
            session_destroy();
            $this->mostrarRespuesta([
                'Result'    => false,
                'Mensaje'   => 'Forbidden',
                'Error'     => 403
            ], 403);

        } else {
            $this->actualizarToken($UserToken);
        }
    }

    private function isLoginPage() 
    {
        return (isset($_POST['UserName']) && $_POST['UserName'] != '') && (isset($_POST['Password']) && $_POST['Password'] != '') ? true : false;
    }

    public function dateFormat($fecha) 
    {
        $date = explode("-", $fecha);
        return $date[2] . "-" . $date[1] . "-" .$date[0];
    }

}