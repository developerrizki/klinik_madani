<?php
function __p($array)
{
    print_r($array);
}

function __vd($vd)
{
    var_dump($vd);
}

function app_shutdown($message) {
    die("BlackCat: $message");
}

function appendPrefix($msisdn)
{
    $startzero  = substr($msisdn, 0, 1);
	$msisdn		= ($startzero == '0') ? '62' . substr($msisdn, 1) : "62$msisdn";

    return $msisdn;
}

function formatLastLogin($time, $host)
{
    if (empty($time) || $time == '0000-00-00 00:00:00')
        return 'Never Logged In';
    else
        return formatDate($time, 'M d, Y H:i:s') . " from $host";
}

function formatDate($date, $format='m/d/Y')
{
    return ($date == null || $date == '0000-00-00 00:00:00' || $date == '0000-00-00') ? '' : date($format, strtotime($date));
}

function zeroExtend($s, $n)
{
    for ($i = 0; $i < $n; $i++) {
        if (strlen($s) == $n) return $s;
        $s = "0".$s;
    }

    return $s;
}

function formatNumber($number)
{
    $number = "$number";
    $number = (strrpos($number, "-")) ? substr($number, 0) : $number;
    $len    = strlen($number);
    $l      = $len;
    $str    = "";

    for ($i = 0; $i < (ceil($len/3)-1); $i++) {
        $str = ",".substr($number, ($l-=3), 3).$str;
    }

    $dotted_number = substr($number, 0, $l).$str;

    return (strrpos($number, "-")) ? "-$dotted_number" : $dotted_number;;
}

function firstCapital($str)
{
    $arrstr = explode(' ', $str);
    for ($i = 0; $i < sizeof($arrstr); $i++)
    {
        if (strlen($arrstr[$i]) > 0) $arrstr[$i][0] = strtoupper($arrstr[$i][0]);
    }
    return implode(' ', $arrstr);
}

function num2String($num)
    {
        $arrNum =
            array(
                '0' => 'nol',
                '1' => 'satu',
                '2' => 'dua',
                '3' => 'tiga',
                '4' => 'empat',
                '5' => 'lima',
                '6' => 'enam',
                '7' => 'tujuh',
                '8' => 'delapan',
                '9' => 'sembilan'
            );

        $res = '';
        $number = $num . '';
        $length = strlen($number);

        for ($i = 0; $i < $length; $i++)
        {
            $residu = $length - $i;
            $reside = $residu % 3;
            $current = $number[$i];
            $arrcur = $arrNum[$current];

            if ($residu == 1)
            {
                $res .= ((($res != '') && ($current == '0')) ? '' : " $arrcur");
            }
            else
            {
                if ($current == '1')
                {
                    if (($reside == 2) && ($number[$i+1] != '0'))
                    {
                        $i++;
                        $residu = $length - $i;
                        $reside = 1;
                        $current = $number[$i];
                        $arrcur = $arrNum[$current];

                        $res .= ($current == '1') ? ' se' : " $arrcur";
                        $res .= 'belas ';
                    }
                    else
                        $res .= ' se';
                }
                elseif ($current == '0')
                {
                    if ($reside != 1) continue;
                    else $res .= ' ';
                }
                else $res .= ' ' . $arrNum[$current] . ' ';

                if (($reside) == 1)
                {
                    if ($residu == 1) continue;
                    do
                    {
                        $reside = $residu % 12;
                        if ($reside == 1) $res .= 'trilyun';
                        elseif ($reside == 10) $res .= 'milyar';
                        elseif ($reside == 7) $res .= 'juta';
                        elseif ($reside == 4) $res .= 'ribu';
                        $residu -= $reside;
                    }
                    while ($residu > 12);
                }
                elseif ($reside == 0) $res .= 'ratus';
                else $res .= 'puluh';
            }
        }

        return $res;
    }

function getPosition($position)
{
	$res = '';

	if (!empty($position)) {
		$pos = explode(" ", $position);

		$lons = explode("(", $pos[0]);
		$lats = explode(")", $pos[1]);

		$res  = array('latitude' => $lats[0], 'longitude' => $lons[1]);

	}

	return $res;
}

function getSQLLimit($page, $itemPerPage)
{
	global $cfg;

	$page			= (empty($page)) ? 1 : $page;
	$itemPerPage	= (empty($itemPerPage)) ? $cfg['sys']['itemPerPage'] : $itemPerPage;
	$limit			= ($page-1) * $itemPerPage;

	$limit 			= "LIMIT $itemPerPage OFFSET $limit";

	return $limit;
}

function getTimeAgo($time)
{
	$now	= time();
	$ctime	= strtotime($time);

	$lap	= $now - $ctime;
	$str    = '';

	if ($lap < 60)
		$str = 'seconds ago';
	else if ($lap < 3600) {
		$minutes = ceil($lap/3600);
		$str     = "$minutes minute" . (($minutes == 1) ? "" : "s") . " ago";
	} else if ($lap < 86400) {
		$hours   = ceil($lap/86400);
		$str     = "$hours hour" . (($hours == 1) ? "" : "s") . " ago";
	} else if ($lap < 604800) {
		$days	 = ceil($lap/604800);
		$str     = "$days day" . (($days == 1) ? "" : "s") . " ago";
	} else if ($lap < 2592000) {
		$weeks   = ceil($lap/604800);
		$str     = "$weeks week" . (($weeks == 1) ? "" : "s") . " ago";
	}

	return $str;
}

function sendHttpPost($url,$parameter){
        
    $length = strlen($parameter);
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE,1);
    $response = curl_exec($ch);
    //die($url.$parameter." Response:".$response);  
    return $response;
}
    
function sendHttpGet($url,$parameter=""){
    
    if($parameter!=""){
        $parameters = "?".$parameter;
    }
    
  //$start    = time();
  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.$parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE,1);
    $response = curl_exec($ch);
    //echo(curl_errno($ch));
    //die($url.$parameter." Response:".$response);  
    
  //$end  = time();
  
  return $response;
}

function ambilAgenInfo($kode_agen){
        
        $Agen  =  new Agen();
        
        $return_value = $Agen->getAgenInfo($kode_agen);
        
        $decode = json_decode($return_value);
        
        // var_dump($return_value);
        //var_dump($decode);exit;
        
        //cek status
        if($decode->tiketux->status!="OK"){
            $return['status']           = "GAGAL";
            return "";
        }
    
        $return["kode"]         = $decode->tiketux->results->kode;
        $return["nama"]         = $decode->tiketux->results->nama;
        $return["alamat"]       = $decode->tiketux->results->alamat;
        $return["kota"]         = $decode->tiketux->results->kota;
        $return["telp"]         = $decode->tiketux->results->telp;
        $return["website"]      = $decode->tiketux->results->website;
        $return["logo"]         = $decode->tiketux->results->logo->big; 

        return $return;
    }

?>