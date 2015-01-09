<?php
function formatStatus($aktif)
{
    $style = '';

    if ($aktif == 0) {
        $style = "background:#ff0000;color:#ffffff";
    }

    return $style;
}

function formatDevice($man, $model) {
    return "$man $model";
}

function getResStatus($bayar, $batal)
{
    if ($batal == 1) {
        $status = 'Batal';
    } else {
        $status = ($bayar  == 1) ? 'Dibayar' : 'Belum Dibayar';
    }

    return $status;
}

function formatResStatus($bayar, $batal)
{
    if ($batal == 1) {
        $style = "background:#ff0000;color:#ffffff";
    } else {
        $style = ($bayar  == 1) ? "background:#12b812;color:#ffffff" : "background:#feca3d;color:#ffffff";
    }

    return $style;
}
?>