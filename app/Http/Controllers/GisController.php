<?php
namespace App\Http\Controllers;
class GisController extends Controller
{
public function index()
{
return view('map');
}
public function getGadmGeoJson(string $level)
{
if (!in_array($level, ['0', '1', '2', '3', '4'])) {
return response()->json(['message' => 'Level tidak valid'],
400);
}
// Path ke file JSON GADM sesuai level
$filePath =
storage_path("app/public/json/gadm/gadm41_IDN_{$level}.json");
if (!file_exists($filePath)) {
return response()->json([
'message' => "File gadm41_IDN_{$level}.json tidak ditemukan
pada path: {$filePath}"
], 404);
}
$content = file_get_contents($filePath);
return response($content)
->header('Content-Type', 'application/json')
->header('Content-Encoding', 'none');
}
}
