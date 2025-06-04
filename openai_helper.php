<?php
function obtenerEtiquetasYCarpetaOpenAI($ruta_archivo, $nombre_archivo)
{
    $openai_api_key = 'TU_API_KEY_OPENAI'; // <-- Cambia por tu API KEY
    $openai_assistant_id = 'TU_ASSISTANT_ID'; // <-- Cambia por tu Assistant ID

    // 1. Subir el archivo a OpenAI
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/files");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $openai_api_key"
    ]);
    $postfields = [
        "purpose" => "assistants",
        "file" => new CURLFile($ruta_archivo, mime_content_type($ruta_archivo), $nombre_archivo)
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $response = curl_exec($ch);
    curl_close($ch);

    $file_data = json_decode($response, true);
    $file_id = $file_data['id'] ?? null;
    if (!$file_id) {
        return [[], ''];
    }

    // 2. Crear un mensaje para el assistant
    $prompt = "Analiza el archivo adjunto y sugiere etiquetas (máximo 5, separadas por coma) y un nombre de carpeta adecuado para clasificarlo. Devuelve la respuesta en JSON con las claves 'etiquetas' y 'carpeta'.";

    // 3. Crear un thread y enviar el mensaje con el archivo adjunto
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/threads");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $openai_api_key",
        "Content-Type: application/json"
    ]);
    $data = [
        "messages" => [
            [
                "role" => "user",
                "content" => $prompt,
                "attachments" => [
                    [
                        "file_id" => $file_id,
                        "tools" => ["code_interpreter"]
                    ]
                ]
            ]
        ]
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $thread_data = json_decode($response, true);
    $thread_id = $thread_data['id'] ?? null;
    if (!$thread_id) {
        return [[], ''];
    }

    // 4. Ejecutar el assistant sobre el thread
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/assistants/$openai_assistant_id/threads/$thread_id/runs");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $openai_api_key",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    $response = curl_exec($ch);
    curl_close($ch);

    $run_data = json_decode($response, true);
    $run_id = $run_data['id'] ?? null;
    if (!$run_id) {
        return [[], ''];
    }

    // 5. Esperar a que termine el run y obtener la respuesta
    $content = '';
    $max_tries = 10;
    $tries = 0;
    do {
        sleep(2);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/threads/$thread_id/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $openai_api_key"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $messages = json_decode($response, true);
        $last_message = end($messages['data']);
        $content = $last_message['content'][0]['text']['value'] ?? '';
        $tries++;
    } while (strpos($content, '{') === false && $tries < $max_tries);

    // 6. Procesar la respuesta de OpenAI
    $json_start = strpos($content, '{');
    $json_end = strrpos($content, '}');
    $json_str = substr($content, $json_start, $json_end - $json_start + 1);
    $result = json_decode($json_str, true);

    $etiquetas = $result['etiquetas'] ?? [];
    $carpeta = $result['carpeta'] ?? '';

    return [$etiquetas, $carpeta];
} 