<?php
require_once __DIR__ . '/session.php';
function generate_csrf(){ if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function validate_csrf($t){ return hash_equals($_SESSION['csrf_token']??'', $t??''); }
