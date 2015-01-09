<?php

function status($data){

	$style = '';

	if ($data=='Selesai') {
		$style = "
				<div style='width:60px; height:25px; line-height:25px; background:#00c01b; color:#fff; border-radius:4px;
				border:1px #00a317 solid; font-size:8pt;'>
					".$data."
				</div>
		";
	}

	elseif ($data=='Dirawat') {
		$style = "
				<div style='width:60px; height:25px; line-height:25px; background:#ff3d3d; color:#fff; border-radius:4px;
				border:1px #ce0000 solid; font-size:8pt;'>
					".$data."
				</div>
		";
	}


	return $style;

}

?>