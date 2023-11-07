<?php

use Illuminate\Support\Facades\DB;

function std_get($params = NULL) {
    if ($params != NULL) {
        $query = DB::table($params["table_name"]);
        if (isset($params["select"])) {
            $query->select($params["select"]);
        }
        if (isset($params["where"])) {
            $query->where($params["where"]);
        }
        if (isset($params["special_where"])) {
            foreach ($params["special_where"] as $row) {
                $query->where($row["field_name"], $row["operator"], $row["value"]);
            }
        }

        if (isset($params["where_in"])) {
            $query->whereIn($params["where_in"]["field_name"], $params["where_in"]["ids"]);
        }

        if (isset($params["or_where"])) {
            $query->orWhere($params["or_where"]);
        }
        if (isset($params["join"])) {
            foreach ($params["join"] as $row) {
                if (isset($row["join_type"]) && isset($row["table_name"]) && isset($row["on1"]) && isset($row["operator"]) && isset($row["on2"])) {
                    if (strtolower($row["join_type"]) == "inner") {
                        $query->join($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    elseif (strtolower($row["join_type"]) == "left") {
                        $query->leftJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    elseif (strtolower($row["join_type"]) == "right") {
                        $query->rightJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    
                }
            }
        }
        if (isset($params["order_by"])) {
            foreach ($params["order_by"] as $row) {
                if (isset($row["field"]) && isset($row["type"])) {
                    $query->orderBy($row["field"], $row["type"]);
                }
            }
        }
    
        if (isset($params["group_by"])) {
            $query->groupBy($params["group_by"]);
        }

        if (isset($params["limit"])) {
            $query->limit($params["limit"]);
        }

        if (isset($params["offset"])) {
            $query->offset($params["offset"]);
        }

        if (isset($params["dump"]) && $params["dump"] == true) {
            $query->dump();
        }
    
        if (isset($params["distinct"]) && $params["distinct"] == true) {
            $query->distinct();
        }

        if (isset($params["count"]) && $params["count"] == true) {
            return $query->count();
        }
        if (isset($params["max"])) {
            return $query->max($params["max"]);
        }
        if (isset($params["avg"])) {
            return $query->avg($params["avg"]);
        }
        if (isset($params["is_exist"]) && $params["is_exist"] === true) {
            return $query->exist();
        }
        if (isset($params["doesnt_exist"]) && $params["doesnt_exist"] === true) {
            return $query->doesntExist();
        }
        if (isset($params["first_row"]) && $params["first_row"] === true) {
            return (array) $query->first();
        }
        else{
            return json_decode($query->get()->toJSON(), true);
        }
    }
    else{
        return false;
    }
}
function std_get_raw($params = NULL) {
    
    if ($params != NULL) {
        $query = DB::table($params["table_name"]);
        if (isset($params["select"])) {
            $query->selectRaw($params["select"]);
        }
        if (isset($params["where"])) {
            $query->where($params["where"]);
        }
        if (isset($params["special_where"])) {
            foreach ($params["special_where"] as $row) {
                $query->where($row["field_name"], $row["operator"], $row["value"]);
            }
        }

        if (isset($params["where_in"])) {
            $query->whereIn($params["where_in"]["field_name"], $params["where_in"]["ids"]);
        }

        if (isset($params["or_where"])) {
            $query->orWhere($params["or_where"]);
        }
        if (isset($params["join"])) {
            foreach ($params["join"] as $row) {
                if (isset($row["join_type"]) && isset($row["table_name"]) && isset($row["on1"]) && isset($row["operator"]) && isset($row["on2"])) {
                    if (strtolower($row["join_type"]) == "inner") {
                        $query->join($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    elseif (strtolower($row["join_type"]) == "left") {
                        $query->leftJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    elseif (strtolower($row["join_type"]) == "right") {
                        $query->rightJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                    
                }
            }
        }
        if (isset($params["order_by"])) {
            foreach ($params["order_by"] as $row) {
                if (isset($row["field"]) && isset($row["type"])) {
                    $query->orderBy($row["field"], $row["type"]);
                }
            }
        }
    
        if (isset($params["group_by"])) {
            $query->groupBy($params["group_by"]);
        }

        if (isset($params["group_by_raw"])) {
            $query->groupByRaw($params["group_by_raw"]);
        }

        if (isset($params["limit"])) {
            $query->limit($params["limit"]);
        }

        if (isset($params["offset"])) {
            $query->offset($params["offset"]);
        }

        if (isset($params["dump"]) && $params["dump"] == true) {
            $query->dump();
        }
    
        if (isset($params["distinct"]) && $params["distinct"] == true) {
            $query->distinct();
        }

        if (isset($params["count"]) && $params["count"] == true) {
            return $query->count();
        }
        if (isset($params["max"])) {
            return $query->max($params["max"]);
        }
        if (isset($params["avg"])) {
            return $query->avg($params["avg"]);
        }
        if (isset($params["is_exist"]) && $params["is_exist"] === true) {
            return $query->exist();
        }
        if (isset($params["doesnt_exist"]) && $params["doesnt_exist"] === true) {
            return $query->doesntExist();
        }
        if (isset($params["first_row"]) && $params["first_row"] === true) {
            return (array) $query->first();
        }
        else{
            return $query->get();
        }
    }
    else{
        return false;
    }
}

function std_update($params = NULL)
{
    return DB::table($params["table_name"])->where($params["where"])->update($params["data"]);
}

function std_insert($params = NULL)
{
    if ($params != NULL) {
        if ($params["table_name"] != NULL && $params["data"] != NULL) {
            return DB::table($params["table_name"])->insert($params["data"]);
        }
        else{
            return false;
        }
    }
    else{
        return false;
    }
}

function std_insert_get_id($params = NULL)
{
    if ($params != NULL) {
        if ($params["table_name"] != NULL && $params["data"] != NULL) {
            DB::table($params["table_name"])->insert($params["data"]);
            $id = DB::getPdo()->lastInsertId();
            if($id) {
                return $id;
            } else {
                return false;
            }
        }
        else{
            return false;
        }
    }
    else{
        return false;
    }
}

function std_delete($params = NULL)
{
    return DB::table($params["table_name"])->where($params["where"])->delete();
}

function std_truncate($params = NULL)
{
    return DB::table($params["table_name"])->truncate();
}

function std_delete_special_where($params = NULL)
{
    return DB::table($params["table_name"])->where($params["where_field_name"], $params["where_operator"], $params["where_value"])->delete();
}
function std_upsert($params)
{
    return DB::table($params["table_name"])->upsert($params["values"], $params["special_params"]);
}