<?php
session_start();
include 'connect_db.php';
include 'functions.php';
require_once __DIR__ . '/../init.php';

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? '');
if (!validate_csrf_token($token)) {
    echo json_encode(['status'=>'error','code'=>'csrf','message'=>'Invalid CSRF token']);
    exit;
}

$profileType = clean_input($_POST['profile_type'] ?? null);
$entityId    = clean_input($_POST['entity_id'] ?? null);

$allowedTypes = ['cell', 'church', 'group'];
if (!in_array($profileType, $allowedTypes) || empty($entityId)) {
    echo json_encode(['status'=>'error','code'=>'invalid','message'=>'Invalid profile type or entity']);
    exit;
}

$tableMap = [
    'cell'   => ['table' => 'cells', 'id_field' => 'id', 'name_field' => 'cell_name'],
    'church' => ['table' => 'churches', 'id_field' => 'id', 'name_field' => 'church_name'],
    'group'  => ['table' => 'groups', 'id_field' => 'id', 'name_field' => 'group_name'],
];

$tableInfo = $tableMap[$profileType];

$stmt = $conn->prepare("SELECT {$tableInfo['name_field']} AS name FROM {$tableInfo['table']} WHERE {$tableInfo['id_field']} = ? LIMIT 1");
$stmt->execute([$entityId]);
$entity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entity) {
    echo json_encode(['status'=>'error','code'=>'invalid','message'=>'Entity not found']);
    exit;
}

$_SESSION['admin_type']  = $profileType;
$_SESSION['entity_id']   = $entityId;
$_SESSION['entity_name'] = $entity['name'];

echo json_encode(['status'=>'success']);
$_SESSION['entity_name'] = $entity['name'];

echo 'success';
