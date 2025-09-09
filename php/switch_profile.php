<?php
session_start();
include 'connect_db.php';
include 'functions.php';

$profileType = clean_input($_POST['profile_type'] ?? null);
$entityId    = clean_input($_POST['entity_id'] ?? null);

// Validate allowed profile types
$allowedTypes = ['cell', 'church', 'group'];
if (!in_array($profileType, $allowedTypes) || empty($entityId)) {
  echo 'invalid';
  exit;
}

// Fetch the entity name
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
  echo 'invalid';
  exit;
}

// Update the session
$_SESSION['admin_type']  = $profileType;
$_SESSION['entity_id']   = $entityId;
$_SESSION['entity_name'] = $entity['name'];

echo 'success';
