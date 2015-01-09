/**
 * Grid functions
 *
 * Lorensius W. L. T <lorenz@londatiga.net>
 */

function CheckAll(id)
{
    var checkboxes  = $('.checkbox');
    var cb          = 'cb_' + id + '[]';

    for (i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].name == cb) checkboxes[i].checked = (checkboxes[i].checked) ? false : true;
    }

    enableButton(id);
}

function unCheckAll(id)
{
    var checkboxes  = $('.checkbox');
    var cb          = 'cb_' + id + '[]';

    for (i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].name == cb) checkboxes[i].checked = false;
    }

    enableButton(id);
}

function enableButton(id)
{
    var checkboxes  = $('.checkbox');
    var buttons     = $('.button');
    var checked     = true;
    var cb          = 'cb_' + id + '[]';
    var btn         = 'btn' + id;
    var ename       = '';

    for (i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].name == cb) if (checkboxes[i].checked) checked = false;
    }

    for (i = 0; i < buttons.length; i++) {

        ename = buttons[i].name.substring(0, btn.length);

        if (ename == btn) buttons[i].disabled = checked;
    }
}

function showGridSearch(id)
{
    $('#GridSearch_' + id).slideToggle('normal');
}

function gridAdd()
{
	// var url = window.location.pathname;
    document.location = CUR_URL_PATH + ((CUR_URL_PATH.indexOf('/', CUR_URL_PATH.length-1) == -1) ? '/add' : 'add');
   	// document.location = url + '/add';
}

function gridPrint(url)
{
    window.open(url,'','scrollbars=yes,toolbar=no,resizable=yes,location=no,directories=no,status=no,menubar=0,left=0,top=0,width=800,height=600');
}

function showLoading(container, msg)
{
    msg             = (msg == '') ? 'loading' : msg;
    var height      = $('#' + container).height();
	var width		= $('#' + container).width();
 //    var loadingImg  = ROOT_URL + '/themes/default/images/loading.gif';

 //    var loading     = "<div class='loading'><span style='vertical-align:middle'><img src='" + loadingImg + "'></span>"
 //                    + "&nbsp;" + msg + " ...</div>";

 //    $('#' + container).append("<div id='loading-overlay'>" + loading + "</div>");

	// var top 		= height/2 * -1;
	// var left		= (width/2) - 125;

 //    $('#loading-overlay').height(height).css({'position': 'relative', 'top': top, 'left': left, 'width': '100%','z-index': 5000 });
}

function splashSuccess(container)
{
    var height      = $('#' + container).height();
	var width		= $('#' + container).width();

    var success		= "<div class='splash-success'>Delete success!</div>";

    $('#' + container).append("<div id='loading-overlay'>" + success + "</div>");


    var top 		= height/2 * -1;
	var left		= (width/2) - 125;

    $('#loading-overlay').height(height).css({'position': 'relative', 'top': top, 'left': left, 'width': '100%','z-index': 5000 });
}

function splashError(container)
{
    var height      = $('#' + container).height();
	var width		= $('#' + container).width();

    var success		= "<div class='splash-error'>Delete failed!</div>";

    $('#' + container).append("<div id='loading-overlay'>" + success + "</div>");

    var top 		= height/2 * -1;
	var left		= (width/2) - 125;

    $('#loading-overlay').height(height).css({'position': 'relative', 'top': top, 'left': left, 'width': '100%','z-index': 5000 });
}

function removeLoading()
{
    $('#loading-overlay').remove();
}

function loadDataContainer(url, id)
{
    $(document).ready(function() {
        var container   = 'GridContainer_' + id

        // showLoading(container, 'loading');

		$.ajax({
			type : 'GET',
			url : url,
			dataType : 'HTML',
			success : function(data) {
				removeLoading();

                $('#' + container).html(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert('Transport error');
			}
		});
    });
}

function deleteItem(id1, id2)
{
	$(document).ready(function() {
		var cfm = confirm('Delete selected item?');

		if (cfm) {
			var container 	= 'GridContainer_' + id2;
			var data   		= 'cb=' + id1 + '&gid=' + id2 + '&gajax=1';
			// var url 		= window.location.pathname;
			// url 			= url + '/delete';
			var url    		= CUR_URL_PATH + ((CUR_URL_PATH.indexOf('/', CUR_URL_PATH.length-1) == -1) ? '/delete' : 'delete');

            //showLoading(container, 'loading');

            $.ajax({
                type : 'POST',
                url : url,
                dataType : 'html',
				data: data,
                success : function(data) {
					//removeLoading();

					$('#' + container).html(data);

					if (data != '')
						splashSuccess(container);
					else
						splashError(container);

					setTimeout('removeLoading()', 1000);
                },

                error : function(XMLHttpRequest, textStatus, errorThrown) { alert('Delete error'); }
            });
		}
	});
}

function deleteMultiple(id)
{
	$(document).ready(function() {
		var cfm = confirm('Delete selected item(s)?');

		if (cfm) {
			var value    	= '';
			var container	= 'GridContainer_' + id;
			var checkboxes  = $('.checkbox');
			var cb          = 'cb_' + id + '[]';

			for (i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].name == cb && checkboxes[i].checked) value = value + checkboxes[i].value + ':';
			}

			if (value.lastIndexOf(':') == value.length -1) {
				value = value.substring(0, value.length-1);
			}
			// var url = window.location.pathname;
			// url 	= url + '/delete';
			var url 	= CUR_URL_PATH + ((CUR_URL_PATH.indexOf('/', CUR_URL_PATH.length-1) == -1) ? '/delete' : 'delete');
			var data 	= 'cb=' + value + '&gid=' + id + '&gajax=1';

			//showLoading(container, 'loading');

			$.ajax({
				type : 'POST',
				url : url,
				dataType : 'html',
				data: data,
				success : function(data) {
					//removeLoading();

					$('#' + container).html(data);

					if (data != '')
						splashSuccess(container);
					else
						splashError(container);

					setTimeout('removeLoading()', 1000);
				},

				error : function(XMLHttpRequest, textStatus, errorThrown) { alert('Delete error'); }
			});
		} else { unCheckAll(id); }
	});
}

function searchGrid(id)
{
	$(document).ready(function() {
		var filter    = $('#gfilter_' + id).val();
		var keyword   = $('#gkeyword_' + id).val();
		var items     = $('#gitems_' + id).val();
		var matchcase = $('#gmatchcase_' + id).attr('checked') ? 1 : 0;
		var container = 'GridContainer_' + id

		if (keyword == '' && filter != 'all') {
			alert('Please enter keyword!');

			return;
		}

		keyword  = (filter == 'all') ? '' : keyword;

		var data = 'search=1&gajax=1&gid=' + id + '&filter=' + filter + '&keyword=' + keyword + '&matchcase=' + matchcase + '&items=' + items;

		// showLoading(container, 'loading');

		$.ajax({
			type : 'POST',
			url : CUR_URL,
			dataType : 'html',
			data: data,
			success : function(data) {
				//removeLoading();

				$('#' + container).html(data);
			},

			error : function(XMLHttpRequest, textStatus, errorThrown) { alert('Searching error'); }
		});
	});
}