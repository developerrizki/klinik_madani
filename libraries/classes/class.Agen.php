<?php
class Agen{

	//KAMUS GLOBAL
	var $id_file;
	var $url_class;

	//CONSTRUCTOR
	public function __construct(){
		global $cfg;
		global $sessObj;

		$this->id_file="AGEN";
	}

	//METHODE
	function getAgen(){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		
		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/agen.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/agen.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	function getAgenInfo($kode_agen){
		global $cfg;
		global $sessObj;

		$accessToken	= $sessObj->getVar("accessToken");

		$parameters		= "kode=$kode_agen";

		return sendHttpGet($cfg['sys']['apiUrl']."agen/info.json",$parameters);
	}

	public function getInfo($kode_agen=""){
		global $cfg;
		global $sessObj;

		$accessToken	= $sessObj->getVar("accessToken");

		$parameters		= "kode=".$kode_agen."&auth_access_token=$accessToken";

		return sendHttpGet($cfg['sys']['apiUrl']."agen/info.json",$parameters);
	}
	
	public function getKeberangkatan($kode_agen="",$gbk=1){
		global $cfg;
		global $sessObj;

		$accessToken	= $sessObj->getVar("accessToken");

		$parameters		= "kode=".$kode_agen."&gbk=".$gbk."&auth_access_token=$accessToken";

		return sendHttpGet($cfg['sys']['apiUrl']."agen/jurusan.json",$parameters);
	}

	public function ubahPassword($oldpassword,$newpassword) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		$params['oldpassword'] 		= $oldpassword;
		$params['newpassword'] 		= $newpassword;
		
		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."keagenan/user/password.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."keagenan/user/password.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
		
	}

	public function ubahProfile($newname,$newemail,$newkota,$newalamat,$newphone) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		$params['nama'] 		= $newname;
		$params['email'] 		= $newemail;
		$params['nohp'] 		= $newphone;
		$params['alamat'] 		= $newalamat;
		$params['kota'] 		= $newkota;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."keagenan/user/edit.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."keagenan/user/edit.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
		
	}

		public function getChart($periode=""){
			global $cfg;
			global $sessObj;

			$reqSignature	= new RequestSignature();
			$params 		= array();

			$params['auth_nonce']	  	= $reqSignature->createNonce(true);
			$params['auth_timestamp'] 	= time();
			$params['auth_client_id'] 	= $cfg['sys']['client_id'];
			
			$params['periode'] 			= $periode;
			
			$accessToken				= $sessObj->getVar("accessToken");
			$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

			if (!empty($accessToken)) {
				$params['auth_access_token'] = $accessToken;
				$key 	= $accessToken;
				$secret = $accessTokenSecret;
			} else {
				$key 		= $cfg['sys']['client_id'];
				$secret = $cfg['sys']['client_secret'];
			}

			$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/reservasi/chart.json", $params);
			$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

			$url   = $cfg['sys']['apiUrl']."keagenan/reservasi/chart.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;
			return sendHttpGet($url);
	}

		public function getJadwal($kode_agen="",$jurusan,$tanggal){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$tanggals	= explode("/",$tanggal);

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		$params['agen'] 			= $kode_agen;
		$params['jurusan'] 			= $jurusan;
		$params['tanggal'] 			= $tanggals[2]."-".$tanggals[1]."-".$tanggals[0];

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/jadwal.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/jadwal.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;
		// echo $url;
		// exit();
		return sendHttpGet($url);
	}

	public function getJurusan($asal, $tujuan){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		// $params['auth_nonce']	  = $reqSignature->createNonce(true);
		// $params['auth_timestamp'] = time();
		// $params['auth_client_id'] = $cfg['sys']['client_id'];
		$params['asal'] 		  = $asal;
		$params['tujuan'] 		  = $tujuan;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."jurusan/search.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."jurusan/search.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);

	}

	public function getLayoutKursi($kode_agen,$kode_layout){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$tanggals	= explode("/",$tanggal);

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		$params['agen'] 					= $kode_agen;
		$params['kode'] 					= $kode_layout;

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/kursi/layout.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/kursi/layout.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	public function getKursiBooked($kode_agen,$kode_jadwal,$tanggal){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$tanggals	= explode("/",$tanggal);

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		$params['agen'] 					= $kode_agen;
		$params['jadwal'] 				= $kode_jadwal;
		$params['tanggal'] 				= $tanggals[2]."-".$tanggals[1]."-".$tanggals[0];

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/kursi/booked.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/kursi/booked.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	public function addReservasi(
		$kode_agen,$kode_jadwal,$tanggal_berangkat,
		$nama_pemesan,$alamat_pemesan,$telp_pemesan,
		$email_pemesan,$nomor_kursi,$nama_penumpang,$biayaadmin) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$tanggals	= explode("/",$tanggal_berangkat);

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		$params['kode_agen'] 			= $kode_agen;
		$params['kode_jadwal'] 			= $kode_jadwal;
		$params['tanggal_berangkat']	= $tanggals[2]."-".$tanggals[1]."-".$tanggals[0];
		$params['nama_pemesan'] 		= $nama_pemesan;
		$params['alamat_pemesan'] 		= $alamat_pemesan;
		$params['telp_pemesan'] 		= $telp_pemesan;
		$params['email_pemesan'] 		= $email_pemesan;
		$params['nomor_kursi'] 			= $nomor_kursi;
		$params['nama_penumpang'] 		= $nama_penumpang;
		$params['channel'] 				= $cfg['sys']['channel'];
		$params['admin']				= $biayaadmin;

		$accessToken					= $sessObj->getVar("accessToken");
		$accessTokenSecret				= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."keagenan/reservasi/book.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."keagenan/reservasi/book.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}

	public function Pelunasan($kode_booking,$kode_pembayaran) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		$params['kode_booking'] 		= $kode_booking;
		$params['kode_pembayaran'] 		= $kode_pembayaran;
		
		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."h2h/pay.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."h2h/pay.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
		
	}

	public function loginUser($username,$password){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		//Authentication type 1
		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		//parameter
		$params['userid'] 					= $username;
		$params['password'] 				= $password;

		$key 		= $params['auth_client_id'] ;
		$secret = $cfg['sys']['client_secret'];

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."keagenan/user/login.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		// $url   = $cfg['sys']['apiUrl']."keagenan/user/login.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;
		// echo $url;
		// exit();

		return sendHttpPost($cfg['sys']['apiUrl']."keagenan/user/login.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
		// exit();
	}

	public function addTopUp($jumlah){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		//Authentication type 1
		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		
		//parameter
		$params['jumlah'] 			= $jumlah;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."keagenan/topup.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."keagenan/topup.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}

	public function addHistoryTopup($page,$items){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();


		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		$params['page'] 			= $page;
		$params['items'] 			= $items;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/deposit/history.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/deposit/history.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		// echo $url;
		// exit();

		return sendHttpGet($url);
	}

	public function addMutasi($page,$items){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();


		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		$params['page'] 			= $page;
		$params['items'] 			= $items;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/deposit/mutasi.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/deposit/mutasi.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		// echo $url;
		// exit();

		return sendHttpGet($url);
	}

	public function addHistory($page,$items){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();


		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		$params['page'] 			= $page;
		$params['items'] 			= $items;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/reservasi/history.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/reservasi/history.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		// echo $url;
		// exit();

		return sendHttpGet($url);
	}

	public function viewDetail($code,$telp){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();


		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];
		
		$params['kode'] 			= $code;
		$params['nohp'] 			= $telp;

		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/reservasi/cek.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/reservasi/cek.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		// echo $url;
		// exit();

		return sendHttpGet($url);
	}

	public function getSaldo($username,$password){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		
		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/user/saldo.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/user/saldo.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	public function getProfile(){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  = $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		
		$accessToken		= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."keagenan/user/info.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."keagenan/user/info.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	public function joinUser($userid,$password,$email,$name,$address,$city,$phone){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		//Authentication type 1
		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		//parameter
		$params['userid'] 	= $userid;
		$params['password']	= $password;
		$params['email'] 		= $email;
		$params['name'] 		= $name;
		$params['address'] 	= $address;
		$params['city'] 		= $city;
		$params['phone'] 		= $phone;

		$key 		= $params['auth_client_id'] ;
		$secret = $cfg['sys']['client_secret'];

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."user/join.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."user/join.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}

	public function resetPassword($email){

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		//Authentication type 1
		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];

		//parameter
		$params['email'] 					= $email;

		$key 		= $params['auth_client_id'] ;
		$secret = $cfg['sys']['client_secret'];

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."user/forgotpassword.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."user/forgotpassword.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}

	public function cekBook($kode_booking,$email, $kodebayar=''){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		$params['kode'] 					= $kode_booking;
		$params['email'] 					= $email;
		$params['kodebayar'] = $kodebayar;

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."reservasi/cek.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."reservasi/cek.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	public function getBiayaAdmin($kode_payment,$kode_agen){
		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] = time();
		$params['auth_client_id'] = $cfg['sys']['client_id'];
		$params['kode'] 					= $kode_payment;
		$params['agen'] 					= $kode_agen;

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret	= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 		= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("GET", $cfg['sys']['apiUrl']."payment/admin.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		$url   = $cfg['sys']['apiUrl']."payment/admin.json" . '?' . $reqSignature->normalizeParams($params) . '&auth_signature=' . $signature;

		return sendHttpGet($url);
	}

	//lorenz
	public function payMandirieMoney($data) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];

		$params['data'] 			= $data;
		
		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret			= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."mandiriecash/payment.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."mandiriecash/payment.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}

	public function payNiaga(
		$RefNo,
			$TransId,
			$AuthCode,
			$Status,
			$ErrDesc,
			$Signature,
			$Mode) {

		global $cfg;
		global $sessObj;

		$reqSignature	= new RequestSignature();
		$params 		= array();

		$params['auth_nonce']	  	= $reqSignature->createNonce(true);
		$params['auth_timestamp'] 	= time();
		$params['auth_client_id'] 	= $cfg['sys']['client_id'];

		$params['RefNo'] 			= $RefNo;
		$params['TransId'] 			= $TransId;
		$params['AuthCode'] 		= $AuthCode;
		$params['Status'] 			= $Status;
		$params['ErrDesc'] 			= $ErrDesc;
		$params['Signature'] 		= $Signature;
		$params['Mode'] 			= $Mode;

		$accessToken				= $sessObj->getVar("accessToken");
		$accessTokenSecret			= $sessObj->getVar("accessTokenSecret");

		if (!empty($accessToken)) {
			$params['auth_access_token'] = $accessToken;
			$key 	= $accessToken;
			$secret = $accessTokenSecret;
		} else {
			$key 	= $cfg['sys']['client_id'];
			$secret = $cfg['sys']['client_secret'];
		}

		$baseSignature = $reqSignature->createSignatureBase("POST", $cfg['sys']['apiUrl']."e2pay/payment.json", $params);
		$signature     = $reqSignature->createSignature($baseSignature, $key, $secret);

		return sendHttpPost($cfg['sys']['apiUrl']."e2pay/payment.json",$reqSignature->normalizeParams($params).'&auth_signature='.$signature);
	}
	
	function getNilaiVoucher($KodeVoucher){
		global $cfg;
		global $sessObj;

		$parameters		= "kode=$KodeVoucher";


		return sendHttpGet($cfg['sys']['apiUrl']."/voucher/validate.json",$parameters);
	}
	
}
?>