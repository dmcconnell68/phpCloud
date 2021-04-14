<?php

include('../../restrictedpage.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Session variables
$loggedinuser = $_SESSION['username'];
$groupasarray = $_SESSION['groupasarray'];

//Check to make sure user has appropriate group membership for access to this page
$grouparray = $_SESSION['grouparray'];
$adminaccess = $_SESSION['adminaccess'];
$rights = $_SESSION['rights'];
$right_required = 'iam';
$access = 'none';
if (strpos($rights, $right_required) !== false) {
    //Determine if ro or rw access
    $ro = $right_required . '_ro';
    $rw = $right_required . '_rw';
    if (strpos($rights, $ro) !== false) {
        $access = 'ro';
    }
    if (strpos($rights, $rw) !== false) {
        $access = 'rw';
    }
}

//Determine if user has admin access
if ($adminaccess == true) {
    $access = 'rw';
}
else {
    $adminaccess = 0;
}
//$access = 'rw';

if ($access == 'none') {
    echo "You do not have access to this feature!";
    return false;
}

?>
<link rel="stylesheet" href="//select2.github.io/select2/select2-3.5.2/select2.css">

<style>
    /* Works on Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: grey;
    }

    /* Works on Chrome, Edge, and Safari */
    *::-webkit-scrollbar {
        width: 8px;     /* width of the entire scrollbar */
        height: 8px;
    }

    *::-webkit-scrollbar-thumb {
        background-color: grey;
        border-radius: 20px;
        /*border: 2px solid orange;*/
    }

    .myhover{
        background: #b2c2dd;
    }

    .rows .webix_cell:nth-child(2n){
        background-color:#dcdcdc;
    }
    
    .rows .webix_row_select:nth-child(2n){
        background:grey;
    }

    .tooltip {
        position:relative; /* making the .tooltip span a container for the tooltip text */
        border-bottom:1px dashed #000; /* little indicater to indicate it's hoverable */
    }

    .tooltip:before {
        content: attr(data-text); /* here's the magic */
        position:absolute;
        
        /* vertically center */
        top:50%;
        transform:translateY(-50%);
        
        /* move to right */
        left:100%;
        margin-left:15px; /* and add a small left margin */
        
        /* basic styles */
        width:200px;
        padding:10px;
        border-radius:10px;
        background:#000;
        color: #fff;
        text-align:center;

        display:none; /* hide by default */
    }

    .tooltip:hover:before {
        display:block;
    }

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: absolute; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .multiline{   
        line-height: 20px !important;       
        position: relative;
        top: 24px;
    }

    .select2-container .select2-selection--single {
        height: 32px !important;
        width: 200px !important;
    }
    .select2-selection__arrow {
        height: 32px !important;
    }

</style>

<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-users fa-fw "></i>
            Users & Groups
        </h1>
    </div>
</div>


<!-- widget grid -->


    <!-- row -->
    <div class="row">

        <!-- NEW WIDGET START -->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-deletebutton="false" data-widget-colorbutton="false">

                <header>
                    <h3></h3>
                </header>

            <!-- widget div-->
            <div>

                    <!-- widget content -->
                    <div class="widget-body" id="grid1">
                        <div class="widget-body-toolbar">

                            <form role="form" class="form-inline">  

                                <div class="form-group">
                                    <select
                                        id="targetDomain"
                                        name="targetDomain"
                                        required="required"
                                        class="select2"
                                        data-placeholder="Select Domain"
                                        data-close-on-select="true"
                                        data-dropdown-auto-width="true">

                                        <option value="" disabled selected></option>
                                        <option value="test1">test1</option>
                                        <option value="test2">test2</option>
                                        <option value="test3">test3</option>
                                    </select>
                                </div>
                                
                                <div class="btn-group">
                                    <button id="btn_createdomainuser" title="Create domain user" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-user"></i>&nbsp;Create User
                                    </button>
                                    
                                    <button id="btn_createmultipledomainusers" title="Create multiple domain users by uploading spreadsheet" type="button" disabled class="btn btn-default toolbar-button">
                                        <i class="fa fa-upload"></i>&nbsp;Create Multiple Users
                                    </button>

                                    <button id="btn_createdomaingroup" title="Create domain group" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-users"></i>&nbsp;Create Group
                                    </button>
                                </div>      

                                <div class="btn-group">
                                    <button id="btn_exporttoexcel" title="Export to Excel" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-file-excel"></i>
                                    </button>

                                    <button id="btn_showlog" title="Show log for selected row" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-clipboard-list"></i>
                                    </button>

                                    <button id="btn_refreshgrid" title="Refresh grid" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                
                                    <button id="btn_idea" title="Idea or Suggestion" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-lightbulb-on"></i>
                                    </button>

                                    <button id="btn_changelog" title="Change Log" type="button" class="btn btn-default toolbar-button">
                                        <i class="fa fa-question"></i>
                                    </button>
                                </div>
                                
                            </form>

                        </div>

                        <div id="modal_dialog" class="modal">
                            <!-- Modal content -->
                        </div>

                        <div id="Tabs" role="tabpanel">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist" id="tablist">
                                <li class="active"><a href="#jobqueue" aria-controls="jobqueue" role="tab" data-toggle="tab" class="tabs">Job Queue</a></li>
                                <li><a href="#aduser" aria-controls="aduser" role="tab" data-toggle="tab" class="tabs">AD Users</a></li>
                                <li><a href="#adgroup" aria-controls="adgroup" role="tab" data-toggle="tab" class="tabs">AD Groups</a></li>
                                <li><a href="#adusergroup" aria-controls="adgusergroup" role="tab" data-toggle="tab" class="tabs">AD Users/Groups</a></li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content" style="padding-top: 5px">

                                <div role="tabpanel" class="tab-pane active" id="jobqueue">
                                    <div id="jobqueueTable"></div>                    
                                    <div id="jobqueueTable_Paging"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="aduser">
                                    <div id="userTable"></div>                    
                                    <div id="userTable_Paging"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="adgroup">
                                    <div id="groupTable"></div>                    
                                    <div id="groupTable_Paging"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="adusergroup">                                
                                    <div id="usergroupTable"></div>                    
                                    <div id="usergroupTable_Paging"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end widget content -->
                </div>
                <!-- end widget div -->
            </div>
            <!-- end widget -->
        </article>
        <!-- WIDGET END -->
    </div>
    <!-- end row -->

<!-- end widget grid -->


<script type="text/javascript">

$("#targetDomain").select2({
        allowClear: true,
        width: '300px',
        height: '34px',
        placeholder :'select..'
        //data: data
    });

    $(document).ready(function(){

        //$('.tooltips').powerTip();

        $('.toolbar-button').powerTip({
            placement: 'w'      // why doesn't n work on this page????????????????????
        });

        ShowJobqueueGrid();
        activeTab = '#jobqueue';
        click_jobqueue = 1;
        click_aduser = 0;
        click_adgroup = 0;
        click_adusergroup = 0;

        //#######################################
        // Setup tabs and grids
        //#######################################
        $('.tabs').click(function(event) {
            activeTab = $(this).attr('href');

            if (activeTab == '#jobqueue') {
                $('#showlog').prop('disabled', false);
                if (click_jobqueue === 0) {
                    ShowJobqueueGrid();
                    click_jobqueue = 1;
                } else {
                    //refreshJobqueueGrid();
                }
            }

            if (activeTab == '#aduser') {
                $('#showlog').prop('disabled', true);
                selected_tab = 'jobqueue';
                if (click_aduser === 0) {
                    ShowUserGrid();
                    click_aduser = 1;
                } else {
                    //refreshUserGrid();
                }
            }

            if (activeTab == '#adgroup') {
                $('#showlog').prop('disabled', true);
                if (click_adgroup === 0) {
                    ShowGroupGrid();
                    click_adgroup = 1;
                } else {
                    //refreshGroupGrid();
                }
            }

            if (activeTab == '#adusergroup') {
                $('#showlog').prop('disabled', true);
                if (click_adusergroup === 0) {
                    ShowUserGroupGrid();
                    click_adusergroup = 1;
                } else {
                    //refreshUserGroupGrid();
                }
            }
        });


        //#######################################
        // Refresh the grid on the selected tab
        //#######################################
        $('#btn_refreshgrid').on('click', function(e) {
            e.preventDefault();
            console.log('Refresh grid button clicked');
            console.log('activeTab:', activeTab);
            if (activeTab == '#jobqueue') {
                jobqueuegrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });   // Clear the filters
                jobqueuegrid.clearAll();    // Clear the grid
                jobqueuegrid.load("ajax/modules/ent/ajax_ent_getjobqueuedata.php"); // Reload the grid
            }
            if (activeTab == '#aduser') {
                usergrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });   // Clear the filters
                usergrid.clearAll();
                usergrid.load("ajax/modules/ent/ajax_ent_getuserdata.php");
            }
            if (activeTab == '#adgroup') {
                groupgrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });  // Clear the filters
                groupgrid.clearAll();
                groupgrid.load("ajax/modules/ent/ajax_ent_getgroupdata.php");
            }
            if (activeTab == '#adusergroup') {
                usergroupgrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });  // Clear the filters
                usergroupgrid.clearAll();
                usergroupgrid.load("ajax/modules/ent/ajax_ent_getusergroupdata.php");
            }

        });


        //#######################################
        // Export data from grid to Excel
        //#######################################
        // TODO:  Add notiication when button is clicked.  On large tables, they need to filter the data down or it will take a while to
        // export the full table and the browser will appear to be locked up while this happens.
        $('#btn_exporttoexcel').on('click', function(e) {
            e.preventDefault();
            console.log('Export to Excel button clicked');
            if (activeTab == '#jobqueue') {
                var gridname = 'jobqueuegrid';
            }
            if (activeTab == '#aduser') {
                var gridname = 'usergrid';
            }
            if (activeTab == '#adgroup') {
                var gridname = 'groupgrid';
            }
            if (activeTab == '#adusergroup') {
                var gridname = 'usergroupgrid';
            }
            console.log('activeTab:', activeTab);
            console.log('gridname:', gridname);
            webix.toExcel(gridname, {
                spans:true,
                styles:true,
                filename:gridname,
                filterHTML:true
            });
        });


        //#######################################
        // Create a new domain user
        //#######################################
        $('#btn_createdomainuser').on('click', function(e) {    // moved from below document.ready
            e.preventDefault();
            $("#modal_dialog").append('<div id="modal-iframe-createdomainuser" data-iziModal-fullscreen="false"  data-iziModal-title="Create Domain User" data-iziModal-icon="icon-home"></div>');
            var url = "/ajax/modules/ent/ent_form_user.php"
            $("#modal-iframe-createdomainuser").iziModal({
                headerColor: '#337AB7',
                focusInput: true,
                iframe: true,
                iframeHeight: 650,
                iframeURL: url,
                navigateCaption: true,
                borderBottom: true,
                width: 550,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-iframe-createdomainuser").remove();
                },
            });
            $("#modal-iframe-createdomainuser").iziModal('open');               
            
        });

        //#######################################
        // Close create domain user modal.  If insertid is returned, refresh the jobqueue grid and display a toast with the job id
        //#######################################
        window.CloseCreateDomainUserModal = function(insertid){
            $("#modal-iframe-createdomainuser").iziModal("close");

            if (insertid !== undefined) {
                // Refresh job queue grid
                jobqueuegrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });   // Clear the filters
                jobqueuegrid.clearAll();    // Clear the grid
                jobqueuegrid.load("ajax/modules/ent/ajax_ent_getjobqueuedata.php"); // Reload the grid

                iziToast.show({
                        title: '',
                        message: 'Job ' + insertid + ' added to job queue',
                        position: 'topRight',
                        theme: 'light',
                        icon: 'fa fa-check-circle',
                        color: 'green',
                        layout: 2
                });
            }
        };


        //#######################################
        // Create a new domain group
        //#######################################
        $('#btn_createdomaingroup').on('click', function(e) {
            e.preventDefault();
            $("#modal_dialog").append('<div id="modal-iframe-createdomaingroup" data-iziModal-fullscreen="false"  data-iziModal-title="Create Domain Group" data-iziModal-icon="icon-home"></div>');
            var url = "/ajax/modules/ent/ent_form_group.php"
            $("#modal-iframe-createdomaingroup").iziModal({
                headerColor: '#337AB7',
                focusInput: true,
                iframe: true,
                iframeHeight: 650,
                iframeURL: url,
                navigateCaption: true,
                borderBottom: true,
                width: 550,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-iframe-createdomaingroup").remove();
                },
            });
            $("#modal-iframe-createdomaingroup").iziModal('open');
        });

        window.CloseCreateDomainGroupModal = function(insertid){
            $("#modal-iframe-createdomaingroup").iziModal("close");

            if (insertid !== undefined) {
                // Refresh job queue grid
                jobqueuegrid.eachColumn( function(pCol) { var f = this.getFilter(pCol); if (f) if (f.value) f.value = ""; });   // Clear the filters
                jobqueuegrid.clearAll();    // Clear the grid
                jobqueuegrid.load("ajax/modules/ent/ajax_ent_getjobqueuedata.php"); // Reload the grid

                iziToast.show({
                        title: '',
                        message: 'Job ' + insertid + ' added to job queue',
                        position: 'topRight',
                        theme: 'light',
                        icon: 'fa fa-check-circle',
                        color: 'green',
                        layout: 2
                });
            }
        };



        //#######################################
        // Create multiple new domain users by uploading a spreadsheet with the data
        //#######################################
        $('#btn_createmultipledomainusers').on('click', function(e) {
            e.preventDefault();
            $("#modal_dialog").append('<div id="modal-iframe-uploadfile" data-iziModal-fullscreen="false"  data-iziModal-title="Upload File" data-iziModal-icon="icon-home"></div>');
            var url = "/ajax/modules/ent/ent_form_upload.php"
            $("#modal-iframe-uploadfile").iziModal({
                headerColor: '#337AB7',
                focusInput: true,
                iframe: true,
                iframeHeight: 450,
                iframeURL: url,
                navigateCaption: true,
                borderBottom: true,
                width: 550,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-iframe-uploadfile").remove();
                },
            });
            $("#modal-iframe-uploadfile").iziModal('open');
        });

        window.CloseUploadFileModal = function(){
            $("#modal-iframe-uploadfile").iziModal("close");
        };



        //#######################################
        // Show the log for the selected row in the jobqueue grid
        //#######################################
        $('#btn_showlog').on('click', function(e) {
            e.preventDefault();
            var item = jobqueuegrid.getSelectedItem()
            //var requestjson = JSON.stringify(JSON.parse(item.request), null, 4);
            //var responsejson = JSON.stringify(JSON.parse(item.log), null, 4);

            if (item == null) {
                iziToast.show({
                    title: '',
                    message: 'You must select a record first',
                    position: 'topRight',
                    theme: 'light',
                    icon: 'fa fa-exclamation-circle',
                    color: 'red',
                    layout: 2
                });
                return;
            }

            // Show log modal
            $("#modal_dialog").append('<div id="modal-showlog" data-iziModal-fullscreen="false" data-iziModal-icon="icon-home"></div>');
            var requestjson = JSON.stringify(JSON.parse(item.request), null, 4);
            var responsejson = JSON.stringify(JSON.parse(item.log), null, 4);
            $("#modal-showlog").append("Request<br><pre><code>" + requestjson + "</code></pre><br>Response<br><pre><code>" + responsejson + "</code></pre>");
            $("#modal-showlog").iziModal({
                title: "Job ID " + item.id + " Log",
                headerColor: '#337AB7',
                focusInput: true,
                navigateCaption: true,
                borderBottom: true,
                width: 750,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-showlog").remove();
                },
            });
            
            $("#modal-showlog").iziModal('open');
        });


        //#######################################
        // Create a new idea
        //#######################################
        $('#btn_idea').on('click', function(e) {
            e.preventDefault();
            $("#modal_dialog").append('<div id="modal-iframe-idea" data-iziModal-fullscreen="false"  data-iziModal-title="Idea or Suggestion" data-iziModal-icon="icon-home"></div>');
            var url = "/ajax/modules/ent/ent_form_idea.php"
            $("#modal-iframe-idea").iziModal({
                headerColor: '#337AB7',
                focusInput: true,
                iframe: true,
                iframeHeight: 650,
                iframeURL: url,
                navigateCaption: true,
                borderBottom: true,
                width: 800,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-iframe-idea").remove();
                },
            });
            $("#modal-iframe-idea").iziModal('open');
        });

        window.CloseIdeaModal = function(){
            $("#modal-iframe-idea").iziModal("close");
        };


        //#######################################
        // View changelog
        //#######################################
        $('#btn_changelog').on('click', function(e) {
            e.preventDefault();
            $("#modal_dialog").append('<div id="modal-iframe-changelog" data-iziModal-fullscreen="false"  data-iziModal-title="Changelog" data-iziModal-icon="icon-home"></div>');
            var url = "/ajax/modules/ent/iam_changelog.php"
            $("#modal-iframe-changelog").iziModal({
                headerColor: '#337AB7',
                focusInput: true,
                iframe: true,
                iframeHeight: 350,
                iframeURL: url,
                navigateCaption: true,
                borderBottom: true,
                width: 1000,
                radius: 5,
                closeOnEscape: false,
                padding: 10,
                bodyOverflow: false,
                transitionIn: "flipinX",
                closeButton: true,
                overlayClose: false,
                onClosed: function(){
                    $("#modal-iframe-changelog").remove();
                },
            });
            $("#modal-iframe-changelog").iziModal('open');
        });

        window.CloseIdeaModal = function(){
            $("#modal-iframe-changelog").iziModal("close");
        };

        
       /*  var modal = document.getElementById("modal_dialog");
        var btn = document.getElementById("btn_changelog");
        var span = document.getElementsByClassName("close")[0];
        btn.onclick = function() {
            modal.style.display = "block";
            ShowChangeLogGrid();
        }
        span.onclick = function() {
            modal.style.display = "none";
        } */



        //#######################################
        // Jobqueue grid definition
        //#######################################
        function ShowJobqueueGrid() {
            console.log('jobqueuegrid');
            jobqueuegrid = webix.ui({
                container:"jobqueueTable",
                view:"datatable", 
                id:"jobqueuegrid",
                margin:5,
                css:"webix_header_border webix_data_border",
                columns:[
                    { id:"id",	            header:["Job ID",       {content:"textFilter"}],    width:120,  sort:"int"},
                    { id:"sourcemodule",	header:["Source",       {content:"selectFilter"}],  width:100,  sort:"string"},
                    { id:"ticketnumber",	header:["Ticket #",     {content:"textFilter"}], 	width:150,  sort:"string"},
                    { id:"jobtype",	        header:["Job Type",     {content:"selectFilter"}], 	width:250,  sort:"string"},
                    { id:"jobstatus",	    header:["Status",       {content:"selectFilter"}], 	width:150,  sort:"string"},
                    { id:"portaluser",	    header:["Submitted By", {content:"textFilter"}], 	width:165,  sort:"string"},
                    { id:"jobserver",	    header:["Job Server",   {content:"selectFilter"}], 	width:172,  sort:"string"},
                    { id:"timestamp",	    header:["Submit Time",  {content:"textFilter"}], 	width:175,  sort:"string"},
                    { id:"starttimestamp",	header:["Start Time",   {content:"textFilter"}], 	width:175,  sort:"string"},
                    { id:"endtimestamp",	header:["EndTime",      {content:"textFilter"}], 	width:175,  sort:"string"},
                ],
                dragColumn:true,
                css:"rows",
                autoheight:true,
                minHeight:150,
                resizeColumn:true,
                select:"row",
                hover:"myhover",
                scrollY:true,
                scrollX:true,
                navigation:"true",
                url:"ajax/modules/ent/ajax_ent_getjobqueuedata.php",
                pager: { 
                    view: 'pager', size:17, group:5,
                    container:"jobqueueTable_Paging",
                    id:"jobqueuepager",
                    template:function(data, common){
                        var start = data.page * data.size;
                        var end = start + data.size;
                        if (end > data.count) {
                            end = start + (data.count - start);
                        }
                        var total = data.limit * data.size;
                        if (total > data.count) { 
                            total = data.count
                        }
                        var html = "Record  " + (start + 1) + " - " + end + " of " + total;
                        return common.first() + common.prev() + common.pages(data) + common.next() + common.last() + html;
                    },
                },
                on:{
                    onBeforeLoad:function(){
                        this.showOverlay("Loading...");
                    },
                    onAfterLoad:function(){
                        this.hideOverlay();
                        this.sort("id", "desc", "int");
                        this.markSorting("id", "desc");
                    },
                    onAfterFilter:function(){
                        this.getPager().render();
                    }
                },
                
            });	
        } // End of ShowJobqueueGrid

    
        //#######################################
        // AD Users grid definition
        //#######################################
        function ShowUserGrid() {
            console.log('usergrid');
            usergrid = webix.ui({
                container:"userTable",
                view:"datatable", 
                id:"usergrid",
                margin:5,
                css:"webix_header_border webix_data_border",
                columns:[
                    { id:"id",	            header:["ID",               {content:"textFilter"}],    width:75,   sort:"int"},
                    { id:"domain",	        header:["Domain",           {content:"selectFilter"}],  width:185,  sort:"string"},
                    { id:"samaccountname",	header:["samAccountName",   {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"givenname",	    header:["Given Name",       {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"sn",	            header:["Surname",          {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"employeeid",      header:["CSID",             {content:"textFilter"}], 	width:100,  sort:"string"},
                    { id:"email",	        header:["Email Address",    {content:"textFilter"}], 	width:250,  sort:"string"},
                    { id:"upn",	            header:["UPN",              {content:"textFilter"}], 	width:250,  sort:"string"},
                    { id:"enabled",	        header:["Enabled",          {content:"textFilter"}], template:"{common.checkbox()}", 	width:100,  sort:"string"},
                    { id:"passwordexpired",	header:["PW Expired",       {content:"textFilter"}], template:"{common.checkbox()}", 	width:100,  sort:"string"},
                    { id:"created",	        header:["Created",          {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"modified",	    header:["Modified",         {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"first_poll_timestamp",	header:["First Poll",        {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"last_poll_timestamp",	    header:["Last Poll",         {content:"textFilter"}], 	width:200,  sort:"string"},
                ],
                dragColumn:true,
                css:"rows",
                autoheight:true,
                minHeight:150,
                resizeColumn:true,
                select:"row",
                hover:"myhover",
                scrollY:true,
                scrollX:true,
                navigation:"true",
                url:"ajax/modules/ent/ajax_ent_getuserdata.php",
                pager: { 
                    view: 'pager', size:17, group:5,
                    container:"userTable_Paging",
					id:"userpager",
                    template:function(data, common){
                        var start = data.page * data.size;
                        var end = start + data.size;
                        if (end > data.count) {
                            end = start + (data.count - start);
                        }
                        var total = data.limit * data.size;
                        if (total > data.count) { 
                            total = data.count
                        }
                        var html = "Record  " + (start + 1) + " - " + end + " of " + total;
                        return common.first() + common.prev() + common.pages(data) + common.next() + common.last() + html;
                    },
                },
                on:{
                    onBeforeLoad:function(){
                        this.showOverlay("Loading...");
                    },
                    onAfterLoad:function(){
                        this.hideOverlay();
                        this.sort("id", "asc", "int");
                        this.markSorting("id", "asc");
                    },
                    onAfterFilter:function(){
                        this.getPager().render();
                    }
                },
            });	
        }   // End of ShowUserGrid


        //#######################################
        // AD Groups grid definition
        //#######################################
        function ShowGroupGrid() {
            console.log('groupgrid');
            groupgrid = webix.ui({
                container:"groupTable",
                view:"datatable", 
                id:"groupgrid",
                margin:5,
                css:"webix_header_border webix_data_border",
                columns:[
                    { id:"id",	            header:["ID",               {content:"textFilter"}],    width:75,   sort:"int"},
                    { id:"domain",	        header:["Domain",           {content:"selectFilter"}],  width:185,  sort:"string"},
                    { id:"samaccountname",	header:["samAccountName",   {content:"textFilter"}], 	width:300,  sort:"string"},
                    { id:"description",	    header:["Description",      {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"category",	    header:["Category",         {content:"textFilter"}], 	width:100,  sort:"string"},
                    { id:"scope",	        header:["Scope",            {content:"textFilter"}], 	width:175,  sort:"string"},
                    { id:"canonicalname",	header:["Canonical Name",   {content:"textFilter"}], 	width:300,  sort:"string"},
                    { id:"distinguishedname",header:["Distinguished Name",{content:"textFilter"}], 	width:400,  sort:"string"},
                    //{ id:"enabled",	    header:["Enabled",          {content:"textFilter"}], 	width:100,  sort:"string"},
                    { id:"created",	        header:["Created",          {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"modified",	    header:["Modified",         {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"first_poll_timestamp",	header:["First Poll",        {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"last_poll_timestamp",	    header:["Last Poll",         {content:"textFilter"}], 	width:200,  sort:"string"},
                ],
                dragColumn:true,
                css:"rows",
                autoheight:true,
                minHeight:150,
                resizeColumn:true,
                select:"row",
                hover:"myhover",
                scrollY:true,
                scrollX:true,
                navigation:"true",
                url:"ajax/modules/ent/ajax_ent_getgroupdata.php",
                pager: { 
                    view: 'pager', size:17, group:5,
                    container:"groupTable_Paging",
					id:"grouppager",
                    template:function(data, common){
                        var start = data.page * data.size;
                        var end = start + data.size;
                        if (end > data.count) {
                            end = start + (data.count - start);
                        }
                        var total = data.limit * data.size;
                        if (total > data.count) { 
                            total = data.count
                        }
                        var html = "Record  " + (start + 1) + " - " + end + " of " + total;
                        return common.first() + common.prev() + common.pages(data) + common.next() + common.last() + html;
                    },
                },
                on:{
                    onBeforeLoad:function(){
                        this.showOverlay("Loading...");
                    },
                    onAfterLoad:function(){
                        this.hideOverlay();
                    },
                    onAfterFilter:function(){
                        this.getPager().render();
                    }
                },
            });	
        }   // End of ShowGroupGrid


        //#######################################
        // AD Users/Groups grid definition
        //#######################################
        function ShowUserGroupGrid() {
            usergroupgrid = webix.ui({
                container:"usergroupTable",
                view:"datatable", 
                id:"usergroupgrid",
                margin:5,
                css:"webix_header_border webix_data_border",
                columns:[
                    { id:"id",	                header:["ID",                   {content:"textFilter"}],    width:75,   sort:"int"},
                    { id:"domain",	            header:["Domain",               {content:"selectFilter"}],  width:200,  sort:"string"},
                    { id:"group_samaccountname",header:["Group samAccountName", {content:"textFilter"}], 	width:300,  sort:"string"},
                    { id:"user_samaccountname",	header:["User samAccountName",  {content:"textFilter"}], 	width:300,  sort:"string"},
                    { id:"first_poll_timestamp",header:["First Poll",           {content:"textFilter"}], 	width:200,  sort:"string"},
                    { id:"last_poll_timestamp",	header:["Last Poll",            {content:"textFilter"}], 	width:200,  sort:"string"}
                ],
                dragColumn:true,
                css:"rows",
                autoheight:true,
                minHeight:450,
                resizeColumn:true,
                select:"row",
                hover:"myhover",
                scrollY:true,
                scrollX:true,
                navigation:"true",
                url:"ajax/modules/ent/ajax_ent_getusergroupdata.php",
                pager:{
                    template:"{common.first()} {common.prev()} {common.pages()} {common.next()} {common.last()}",
                    container:"usergroupTable_Paging",
                    id:"usergrouppager",
                    width:500,
                    size:17,
                    group:5
                },
                on:{
                    onBeforeLoad:function(){
                    this.showOverlay("Loading...");
                    },
                    onAfterLoad:function(){
                    this.hideOverlay();
                    },
                    onAfterFilter:function(){
                        this.getPager().render();
                    }
                },
            });	
        }   // End of ShowUserGroupGrid

        //#######################################
        // Changelog grid definition
        //#######################################
        /* function ShowChangeLogGrid() {
            changeloggrid = webix.ui({
                container:"changelogTable",
                view:"datatable", 
                id:"changeloggrid",
                margin:5,
                css:"webix_header_border webix_data_border",
                //height:300,
                //width:300,
                columns:[
                    { id:"module",	header:["Module",       {content:"selectFilter"}],  width:100,  sort:"string"},
                    { id:"version",	header:["Version",     {content:"textFilter"}], 	width:100,  sort:"string"},
                    { id:"date",	        header:["Date",     {content:"selectFilter"}], 	width:100,  sort:"string"},
                    { id:"description",	    header:["Change Description", {content:"textFilter"}], css:"multiline",	width:955,  sort:"string"}
                ],
                //dragColumn:true,
                css:"rows",
                autowidth:true,
                autoheight:true,
                fixedRowHeight:false,
                minHeight:150,
                resizeColumn:true,
                select:"row",
                //hover:"myhover",
                //scrollY:true,
                //scrollX:true,
                navigation:"true",
                url:"ajax/modules/ent/ajax_iam_getchangelog.php",
                pager:{
                    template:"{common.first()} {common.prev()} {common.pages()} {common.next()} {common.last()}",
                    container:"changelogTable_Paging",
                    width:500,
                    size:17,
                    group:5
                },
                on:{
                    onBeforeLoad:function(){
                        this.showOverlay("Loading...");
                    },
                    onAfterLoad:function(){
                        webix.delay(function(){
                            this.adjustRowHeight("description", true); 
                            this.render();
                        }, this);

                        this.hideOverlay();

                        //this.sort("id", "desc", "int");
                        //this.markSorting("id", "desc");
                    }
                },
                
            });	
        } // End of ShowChangeLogGrid */

    }); //End of document.ready.function
    
    // pageSetUp() is needed whenever you load a page.
    // It initializes and checks for all basic elements of the page
    // and makes rendering easier.
    pageSetUp();

</script>