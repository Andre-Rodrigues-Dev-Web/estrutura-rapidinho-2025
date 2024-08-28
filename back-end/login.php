<?php
include_once 'db/conn.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));

$response = array();

// Credenciais fixas para login alternativo
$fixedEmail = 'admin';
$fixedPassword = '2802';

// Função para gerar um token simples
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

try {
    if(isset($data->email) && isset($data->password)) {
        $email = $data->email;
        $password = $data->password;
        
        // Primeiro, tenta autenticar com o banco de dados
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                $token = generateToken();
                $response['success'] = true;
                $response['message'] = "Login bem-sucedido";
                $response['token'] = $token;
            } else {
                $response['success'] = false;
                $response['message'] = "Senha incorreta";
            }
        } else {
            // Se não encontrar no banco, tenta as credenciais fixas
            if($email === $fixedEmail && $password === $fixedPassword) {
                $token = generateToken();
                $response['success'] = true;
                $response['message'] = "Login bem-sucedido com credenciais fixas";
                $response['token'] = $token;
            } else {
                $response['success'] = false;
                $response['message'] = "Usuário não encontrado";
            }
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Dados de login incompletos";
    }
} catch(PDOException $e) {
    // Se ocorrer um erro de conexão, tenta as credenciais fixas
    if(isset($data->email) && isset($data->password)) {
        if($data->email === $fixedEmail && $data->password === $fixedPassword) {
            $token = generateToken();
            $response['success'] = true;
            $response['message'] = "Login bem-sucedido com credenciais fixas";
            $response['token'] = $token;
        } else {
            $response['success'] = false;
            $response['message'] = "Credenciais inválidas";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Erro de conexão e dados de login incompletos";
    }
}

echo json_encode($response);
?>