<?php
header('Content-Type: application/json');

echo json_encode(array_merge(['error' => $error], $info ?? []));
