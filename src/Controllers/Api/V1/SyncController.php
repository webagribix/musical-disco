<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response,DB};
use App\Models\ActivityLog;

class SyncController
{
    /** Last-write-wins sync endpoint for offline queued writes */
    public function sync(Request $req, Response $res): never
    {
        $user   = Middleware::requireAuth($req, $res);
        $writes = $req->body()['writes'] ?? [];

        if (!is_array($writes)) $res->unprocessable(['writes' => 'Must be array of write operations']);

        $results = [];
        $db      = DB::getInstance();

        foreach ($writes as $i => $write) {
            $table  = preg_replace('/[^a-z0-9_]/i', '', $write['table']  ?? '');
            $action = strtolower($write['action'] ?? 'upsert');
            $data   = $write['data']   ?? [];
            $id     = isset($write['id']) ? (int)$write['id'] : null;

            if (!$table) { $results[$i] = ['error' => 'No table']; continue; }

            try {
                if ($action === 'insert' || ($action === 'upsert' && !$id)) {
                    $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
                    $newId = $db->insert($table, $data);
                    $results[$i] = ['success' => true, 'id' => $newId];
                } elseif (($action === 'update' || $action === 'upsert') && $id) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $db->update($table, $data, ['id' => $id]);
                    $results[$i] = ['success' => true, 'id' => $id];
                } elseif ($action === 'delete' && $id) {
                    $db->delete($table, ['id' => $id]);
                    $results[$i] = ['success' => true];
                } else {
                    $results[$i] = ['error' => 'Unknown action'];
                }
                ActivityLog::log($user['id'], "sync_{$action}", $table, $id, null, $data, $req->ip());
            } catch (\Throwable $e) {
                $results[$i] = ['error' => $e->getMessage()];
            }
        }

        $res->success(['results' => $results, 'processed' => count($writes)]);
    }
}
