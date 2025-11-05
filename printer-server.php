<?php
// printer-server.php
header('Content-Type: application/json');

// Permitir apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido. Use POST.']);
    exit;
}

// Lê o corpo JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

$texto = $input['texto'] ?? null;
$impressora = $input['impressora'] ?? null;

if (!$texto) {
    http_response_code(400);
    echo json_encode(['error' => 'Campo "texto" é obrigatório']);
    exit;
}

// Monta o comando para enviar à impressora
$cmd = 'echo ' . escapeshellarg($texto) . ' | lp';
if ($impressora) {
    $cmd .= ' -d ' . escapeshellarg($impressora);
}

// Executa o comando e captura saída/erro
exec($cmd . ' 2>&1', $output, $status);

file_put_contents(__DIR__ . '/print_log.txt', 
    date('Y-m-d H:i:s') . " CMD: {$cmd}\n" .
    "STATUS: {$status}\n" .
    "OUTPUT:\n" . implode("\n", $output) . "\n\n",
    FILE_APPEND
);


if ($status === 0) {
    echo json_encode(['success' => true, 'mensagem' => 'Impressão enviada com sucesso']);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Falha ao imprimir',
        'detalhes' => $output
    ]);
}
