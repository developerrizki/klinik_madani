<?php 
function dateEng($date){ 

	$day 	= substr($date,8,2); 
	$month 	= getMonth(substr($date,5,2)); 
	$year 	= substr($date,0,4); 
	
	return $day.' '.$month.' '.$year; 
} 

function getMonth($month){ 
	switch ($month){ 
		case 1: 	return "January"; 	break; 
		case 2: 	return "February"; 	break; 
		case 3: 	return "March"; 	break; 
		case 4: 	return "April"; 	break; 
		case 5: 	return "May"; 		break;
		case 6: 	return "Juny"; 		break; 
		case 7: 	return "July"; 		break; 
		case 8: 	return "August"; 	break; 
		case 9: 	return "September"; break; 
		case 10:	return "October"; 	break; 
		case 11: 	return "November"; 	break; 
		case 12: 	return "December"; 	break; 
	}
} 

function dateEngShort($date){ 

	$day 	= substr($date,8,2); 
	$month 	= getMonthShort(substr($date,5,2)); 
	$year 	= substr($date,0,4); 
	
	return $day.' '.$month.' '.$year; 
} 

function getMonthShort($month){ 
	switch ($month){ 
		case 1: 	return "Jan"; 	break; 
		case 2: 	return "Feb"; 	break; 
		case 3: 	return "Mar"; 	break; 
		case 4: 	return "Apr"; 	break; 
		case 5: 	return "May"; 	break;
		case 6: 	return "Jun"; 	break; 
		case 7: 	return "Jul"; 	break; 
		case 8: 	return "Aug"; 	break; 
		case 9: 	return "Sep"; 	break; 
		case 10:	return "Oct"; 	break; 
		case 11: 	return "Nov"; 	break; 
		case 12: 	return "Dec"; 	break; 
	}
}

function dateIn($tgl){ 

	$tanggal 	= substr($tgl,8,2); 
	$bulan 		= getBulan(substr($tgl,5,2)); 
	$tahun 		= substr($tgl,0,4); 
	
	return $tanggal.' '.$bulan.' '.$tahun; 
} 

function getBulan($bln){ 
	switch ($bln){ 
		case 1: 	return "Januari"; 	break; 
		case 2: 	return "Februari"; 	break; 
		case 3: 	return "Maret"; 	break; 
		case 4: 	return "April"; 	break; 
		case 5: 	return "Mei"; 		break;
		case 6: 	return "Juni"; 		break; 
		case 7: 	return "Juli"; 		break; 
		case 8: 	return "Agustus"; 	break; 
		case 9: 	return "September"; break; 
		case 10:	return "Oktober"; 	break; 
		case 11: 	return "November"; 	break; 
		case 12: 	return "Desember"; 	break; 
	}
}

?>