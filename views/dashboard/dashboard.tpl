<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Dashboard</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Chart by Status
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div id="chartdiv" style='width:100%; height:400px;'></div>
                        <!-- /.chartdiv -->
                    </div>                        
                    <!-- /.panel-body -->
                </div>                    
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<!-- amchart -->
<script type="text/javascript" src="[--ROOT_URL--]/jscript/chart/amchart.js"></script>
<script type="text/javascript" src="[--ROOT_URL--]/jscript/chart/serial.js"></script>
<script type="text/javascript" src="[--ROOT_URL--]/jscript/chart/none.js"></script>

<script>
var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "none",
    "legend": {
        "markerType": "square",
        "position": "right",
        "marginRight": 0,       
        "autoMargins": false
    },
    "pathToImages": "http://www.amcharts.com/lib/3/images/",
    "dataProvider": [{
        "date": "2012-03-01",
        "patient": 20
    }, {
        "date": "2012-03-02",
        "patient": 75
    }, {
        "date": "2012-03-03",
        "patient": 15
    }, {
        "date": "2012-03-04",
        "patient": 75
    }, {
        "date": "2012-03-05",
        "patient": 158
    }, {
        "date": "2012-03-06",
        "patient": 57
    }, {
        "date": "2012-03-07",
        "patient": 107
    }, {
        "date": "2012-03-08",
        "patient": 89
    }, {
        "date": "2012-03-09",
        "patient": 75
    }, {
        "date": "2012-03-10",
        "patient": 132
    }],
    "graphs": [{
        "bullet": "round",
        "id": "g1",
        "bulletBorderAlpha": 1,
        "bulletColor": "#FFFFFF",
        "bulletSize": 7,
        "lineThickness": 2,
        "title": "Patient Finish",
        "type": "smoothedLine",
        "useLineColorForBulletBorder": true,
        "valueField": "patient"
    }],
    
    "dataDateFormat": "YYYY-MM-DD",
    "categoryField": "date",
    "categoryAxis": {
        "parseDates": true
    }
});
</script>