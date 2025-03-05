<?php

// Include database connection
require_once 'test.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode the incoming JSON payload
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate the input
    if (! isset($data['disease_id']) || ! is_numeric($data['disease_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or missing disease_id']);
        exit;
    }

    if (! isset($data['responses']) || ! is_array($data['responses'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Responses must be an array']);
        exit;
    }

    // Validate responses
    foreach ($data['responses'] as $response) {
        if (! isset($response['question_id']) || ! is_numeric($response['question_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or missing question_id in responses']);
            exit;
        }

        if (! isset($response['choice_id']) || ! is_numeric($response['choice_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or missing choice_id in responses']);
            exit;
        }
    }

    // Extract geo-coordinates
    $latitude = isset($data['location']['lat']) ? $data['location']['lat'] : null;
    $longitude = isset($data['location']['long']) ? $data['location']['long'] : null;

    try {
        // Connect to the database
        $db = new PDO($dsn, $username, $password, $options);

        // Check if disease exists
        $stmt = $db->prepare('SELECT disease_id FROM diseases WHERE disease_id = :disease_id');
        $stmt->execute([':disease_id' => $data['disease_id']]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Disease not found']);
            exit;
        }

        // Generate a unique responder name
        $responderName = 'Responder_'.bin2hex(random_bytes(5));

        // Current timestamp for created_at
        $createdAt = date('Y-m-d H:i:s');

        // Insert responder into database
        $stmt = $db->prepare('
            INSERT INTO responders (responder_name, disease_id, latitude, longitude, created_at)
            VALUES (:responder_name, :disease_id, :latitude, :longitude, :created_at)
        ');
        $stmt->execute([
            ':responder_name' => $responderName,
            ':disease_id' => $data['disease_id'],
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':created_at' => $createdAt,
        ]);

        // Get the inserted responder ID
        $responderId = $db->lastInsertId();

        // Insert responses into database
        $stmt = $db->prepare('
            INSERT INTO responses (responder_id, disease_id, question_id, choice_id)
            VALUES (:responder_id, :disease_id, :question_id, :choice_id)
        ');

        foreach ($data['responses'] as $response) {
            $stmt->execute([
                ':responder_id' => $responderId,
                ':disease_id' => $data['disease_id'],
                ':question_id' => $response['question_id'],
                ':choice_id' => $response['choice_id'],
            ]);
        }

        // Return success response
        http_response_code(201);
        echo json_encode([
            'message' => 'Responses saved successfully.',
            'responder_id' => $responderId,
        ]);

    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500);
        echo json_encode(['error' => 'Database error: '.$e->getMessage()]);
    } catch (Exception $e) {
        // Handle general errors
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred: '.$e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
