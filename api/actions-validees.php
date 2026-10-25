<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
function fail(int $code): void { http_response_code($code); echo json_encode(['error'=>'Synchronisation indisponible']); exit; }
$config = require dirname(__DIR__) . '/private/config.php';
$key = $config['api_key'] ?? '';
if (!$key || $key === 'REMPLACER_PAR_CLE_API_GRIST') fail(503);
if (!function_exists('curl_init')) fail(503);
$url = rtrim($config['grist_base'], '/') . '/api/docs/' . rawurlencode($config['document_id']) . '/tables/ACTIONS/records';
$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$key,'Accept: application/json'],CURLOPT_FOLLOWLOCATION=>false]);
$body=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
if ($body === false || $status !== 200) fail(502);
$data=json_decode($body,true);
if (!is_array($data) || !isset($data['records']) || !is_array($data['records'])) fail(502);
$actions=[];
foreach($data['records'] as $record){
  $f=$record['fields'] ?? [];
  if (!is_array($f) || ($f['Statut_automatique'] ?? null) !== 'Validée') continue;
  // Liste blanche : aucun champ interne, aucune décision académique, aucun email.
  $link=$f['Lien_ressource'] ?? '';
  if (!is_string($link) || !filter_var($link,FILTER_VALIDATE_URL) || parse_url($link,PHP_URL_SCHEME)!=='https') $link='';
  $actions[]=[
    'titre'=>is_string($f['Titre'] ?? null)?$f['Titre']:'',
    'college'=>is_scalar($f['College'] ?? null)?(string)$f['College']:'',
    'domaine'=>is_string($f['Domaine'] ?? null)?$f['Domaine']:'',
    'description'=>is_string($f['Description'] ?? null)?$f['Description']:'',
    'date_debut'=>is_string($f['Date_debut'] ?? null)?$f['Date_debut']:'',
    'lien_ressource'=>$link
  ];
}
echo json_encode(['actions'=>$actions],JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
