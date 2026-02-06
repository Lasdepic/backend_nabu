<?php
// Gestion du CORS
header("Access-Control-Allow-Origin: http://localhost:5500"); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-File-Name, X-Force-Replace, Content-Range");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit();
}
