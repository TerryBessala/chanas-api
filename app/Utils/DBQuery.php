<?php


namespace App\Utils;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait DBQuery
{
    public function updateBatch($tableName, $index, $multipleData = [])
    {
        try {
            if (empty ($multipleData)) {
                throw new \ Exception ("Data cannot be empty");
            }
            $firstRow = current($multipleData);
            $updateColumn = array_keys($firstRow);
            //Update by id by default,
            //If there is no ID then the first field is used as the condition
            $referenceColumn = isset ($firstRow [$index]) ?
                $index : current($updateColumn);
            unset ($updateColumn [0]);
            //splicing sql statements
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = " `" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= " WHEN `" . $referenceColumn . "` =? THEN ? ";
                    $bindings [] = $data [$referenceColumn];
                    $bindings [] = $data [$uColumn];
                }
                $setSql .= " ELSE `" . $uColumn . "` END";
                $sets [] = $setSql;
            }
            $updateSql .= implode(", ", $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat("?,", count($whereIn)), ",");
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ") ";
            //Pass in the prepared SQL statement and the corresponding binding data
            return DB::update($updateSql, $bindings);
        } catch (\ Exception $e) {
            Log::info("Batch Update error " . $e->getMessage());
            return false;
        }
    }
}
