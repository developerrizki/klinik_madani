/* Check Character */
function getKey(e){
	if (window.event){ return window.event.keyCode; }
	else if (e){ return e.which;}
	else{return null; }
}

function goodChars(e, field){
	var key, keychar;
	key = getKey(e);
	if (key == null) {return true};
 
	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();
	var goods = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	goods = goods.toLowerCase();
 
	// check goodkeys
	if (goods.indexOf(keychar) != -1){return true;}
	// control keys
	if ( key==null || key==0 || key==8 || key==9 || key==27 ) {return true; }
    
	if (key == 13) {
		var i;
		for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
            break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		return false;
    };
	// else return false
	return false;
}

function goodInts(e, field){
	var key, keychar;
	key = getKey(e);
	if (key == null) {return true};
 
	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();
	var goods = "0123456789";
	goods = goods.toLowerCase();
 
	// check goodkeys
	if (goods.indexOf(keychar) != -1){return true;}
	// control keys
	if ( key==null || key==0 || key==8 || key==9 || key==27 ) {return true; }
    
	if (key == 13) {
		var i;
		for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
            break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		return false;
    };
	// else return false
	return false;
}

/* Check Password*/
function checkPassword(retype){

	var pass 	 = document.getElementById('password');
	var alert  	 = document.getElementById('alert');

	if(pass.value !== ''){
		if(pass.value == retype){
			alert.innerHTML 	= 'Kata sandi Cocok!';
			alert.style.color 	= '#72c142';
		}
		else{
			alert.innerHTML 	= 'Kata sandi Tidak cocok!';
			alert.style.color 	= '#e11a1a';
		}	
	}

}