<?php
    function dateConvertToString($data = NULL)
    {
        $date_temp = date("Y-m-d", strtotime($data));

        $date_comment=date_create(date("Y-m-d", strtotime($data)));
        $today=date_create(date("Y-m-d"));
        $diff=date_diff($date_comment,$today);

        if ($diff->format("%a") == 0) {
            $date = "Hari Ini";
        } else if ($diff->format("%a") == 1) {
            $date = "Kemarin";
        } else {
            $date = $diff->format("%a")." Hari Yang Lalu";
        }
        return $date;
    }

    function get_contact()
    {
        return std_get([
            "select" => ["contact_name"],
            "table_name" => "contacts",
            "first_row" => true
        ]);
    }
?>