<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv='content-type' content='text/html; charset=utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Panel">
    <meta name="author" content="">
	
	<title>[--TITLE--]</title>
	
	<link rel='shortcut icon' href="[--ROOT_URL--]/themes/[--THEME--]/images/logo_pasien_2.png">
	<!-- Bootstrap Core CSS -->
    <link href="[--ROOT_URL--]/themes/[--THEME--]/css/bootstrap/bootstrap.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="[--ROOT_URL--]/themes/[--THEME--]/css/bootstrap/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="[--ROOT_URL--]/themes/[--THEME--]/css/bootstrap/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="[--ROOT_URL--]/themes/[--THEME--]/css/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<script language="JavaScript">
	    var ROOT_URL      	 = '[--ROOT_URL--]';
	    var CUR_URL       	 = '[--CUR_URL--]';
		var CUR_URL_PATH     = '[--CUR_URL_PATH--]';
	    var QUERY_STRING  	 = '[--QUERY_STRING--]';
	    var THEME            = '[--THEME--]';
	</script>
	<!-- Form Validation -->
    <script type="text/javascript" src="[--ROOT_URL--]/jscript/form-validation.js"></script>
    <!-- jQuery -->
    <script type="text/javascript" src="[--ROOT_URL--]/jscript/jquery.js"></script>
    <!-- jQuery Ui-->
    <script type="text/javascript" src="[--ROOT_URL--]/jscript/jquery-ui.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="[--ROOT_URL--]/jscript/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="[--ROOT_URL--]/jscript/js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="[--ROOT_URL--]/jscript/js/sb-admin-2.js"></script>
    <!-- jquery datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	
	
</head>

<body>

<!-- HEADER -->
<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html">Klinik Madani</a>
        </div>
    <!-- /.navbar-header -->
        <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-messages">
                    <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
                    <li class="divider"></li>
                    <li><a href="[--ROOT_URL--]/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

<!-- NAV -->
        <div class="navbar-default sidebar" role="navigation">
	    	<div class="sidebar-nav navbar-collapse">
		        <ul class="nav" id="side-menu">
		            [--MENU--]
		        </ul>
	        </div>
	                <!-- /.sidebar-collapse -->
	    </div>
	            <!-- /.navbar-static-side -->
	 </nav>

<!-- Content -->

[--CONTENT--]

</div>

</body>
</html>