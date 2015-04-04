<?php
class qa_html_theme_layer extends qa_html_theme_base {
	var $timeline_plugin_directory;
	var $timeline_plugin_url;
	function qa_html_theme_layer($template, $content, $rooturl, $request)
	{
		global $qa_layers;
		$this->timeline_plugin_directory = $qa_layers['timeline Layer']['directory'];
		$this->timeline_plugin_url = $qa_layers['timeline Layer']['urltoroot'];
		qa_html_theme_base::qa_html_theme_base($template, $content, $rooturl, $request);
	}
	function head_css() {
		global $qa_request;
		if ( ($qa_request == 'timeline') && (qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) )
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'. qa_opt('site_url') . $this->timeline_plugin_url.'include/style.css'.'"/>');
		qa_html_theme_base::head_css();
	}	
	function head_script(){
		qa_html_theme_base::head_script();
		global $qa_request;
		if ( ($qa_request == 'timeline') && (qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) )
				$this->output('<script type="text/javascript" src="'. qa_opt('site_url') . $this->timeline_plugin_url .'include/easyResponsiveTabs.js"></script>');  
	}	
	function body_footer(){
		global $qa_request;
		if ( ($qa_request == 'timeline') && (qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) )
			$this->output(
				'<script type="text/javascript">',
				'$("#verticalTab").easyResponsiveTabs({',
				'   type: "vertical",',      
				'   width: "auto",',
				'   fit: true',
				'});',
				'</script>'
			);
		qa_html_theme_base::body_footer();
	}
	function doctype(){
		// Setup Navigation
		global $qa_request;
		if (qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN){
			if (qa_opt('tl_link_nav')) {
				$this->content['navigation']['main']['timeline'] = array(
					'label' => 'timeline',
					'url' => qa_path_html('timeline'),
					'opposite' => true,
				);
				if($qa_request == 'timeline') {
					$this->content['navigation']['main']['timeline']['selected'] = true;
				}
			}
			if($qa_request == 'timeline') {
					$this->template="q2a_timeline";
					$this->content['site_title']="Reports";
					$this->content['error']="";
					$this->content['suggest_next']="";
					$this->content['title']="Event Timeline";
			}
			if($qa_request == 'timeline') {
				require_once QA_INCLUDE_DIR.'qa-db-recalc.php';
				require_once QA_INCLUDE_DIR.'qa-app-admin.php';
				require_once QA_INCLUDE_DIR.'qa-db-admin.php';

				$this->content['custom']=$this->timeline();
			}
		}else{
			if($qa_request == 'timeline')
				$this->content['custom'] = '<div class="qa-error">Nosy little thing you are, aren\'t you?</div> <strong>Log in as Administrator to see all those precious information.</strong>';
		}
		qa_html_theme_base::doctype();
	}
	
	function timeline(){
		$query = "SELECT `datetime`, `handle`, `event`, `params` FROM `qa_eventlog`";

		$result=qa_db_query_raw($query);
		$arr=array();
		$eventLog="";
		if (mysqli_num_rows($result) > 0) {
		    // output data of each row
		    while($row = mysqli_fetch_assoc($result)) {

		    	$a=array('datetime' =>$row["datetime"] ,'handle' =>$row["handle"],'event' =>$row["event"] ,'params'=>$row["params"]);
		    	array_push($arr, $a);
		        //echo "datetime: " . $row["datetime"]. " - handle: " . $row["handle"]. " - event: " . $row["event"]. "<br>";
		    }
		} 
		$i=0;
		$param="";
    	foreach ($arr as &$entry) {
    			// $param=$param."dor".array_values($entry)[0];
    			if(is_null(array_values($entry)[1])){
    			$param=$param.' newData.push({id: '.$i.',params: \''.array_values($entry)[3].'\' ,events: \''.array_values($entry)[2].'\',username:\'Not Register\' , start: \''.array_values($entry)[0].'\'});';
    			}
    			else{
    				$param=$param.' newData.push({id: '.$i.',params: \''.array_values($entry)[3].'\' ,events: \''.array_values($entry)[2].'\',username:\''.array_values($entry)[1].'\' , start: \''.array_values($entry)[0].'\'});';
    			}
	     	  // $param=$param.' \n newData.push({id: '.$i.',params: '.array_values($entry)[3].' ,events: '.array_values($entry)[2].',username:'.array_values($entry)[1].', content: '.array_values($entry)[1].'+" - "+'.array_values($entry)[2].'  , start: '.array_values($entry)[0].'}); // much much faster than now.clone add days'.;
	     	 $i++;
	    	# code...
    	}

    


		$content= '
 <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script src="' . qa_opt('site_url') . $this->timeline_plugin_url .'include/vis/dist/vis.js"></script>
  <link href="' . qa_opt('site_url') . $this->timeline_plugin_url .'include/vis/dist/vis.css" rel="stylesheet" type="text/css" />
<p><strong>Filter:</strong></p>
<p>
By Username - :
   <select id="users">
  <option id="empty"></option>
</select>
By Action:
<select id="actions">
  <option id="empty"></option>
</select>
    <input id="draw" type="button" value="filter">
</p>
<div id="visualization" style="height="100px""></div>

<script>
  // create a dataset with items
  var now = moment().minutes(0).seconds(0).milliseconds(0);
  var items = new vis.DataSet({
    type: {start: \'ISODate\', end: \'ISODate\' }
  });
  var users=[];
  var actions=[];
 var newData = [];
  // create data
  function createData() {
   newData.clear;
    '.$param.'

   newData.forEach(function(entry) {
        if($.inArray(entry[\'username\'],users)==-1)
          		users.push(entry[\'username\'])
        if($.inArray(entry[\'events\'],actions)==-1)
          actions.push(entry[\'events\'])});
      for (var i = users.length - 1; i >= 0; i--) {
      document.getElementById(\'users\').innerHTML+=\'<option id=\'+users[i]+\'>\'+users[i]+\'</option>\'
    };
       for (var i = actions.length - 1; i >= 0; i--) {
      document.getElementById(\'actions\').innerHTML+=\'<option id=\'+actions[i]+\'>\'+actions[i]+\'</option>\'
  };
  items.clear();
    items.add(newData);
  }
  createData();

   function filter(){
    // retrieve a filtered subset of the data
    var e = document.getElementById("users");
  var username = e.options[e.selectedIndex].value;
     e = document.getElementById("actions");
  var action = e.options[e.selectedIndex].value;
    items.clear();
    items.add(newData);
    var item = items.get({
      filter: function (item) {
        if(action!="" && username!=""){
        return item.username == username && item.events == action;
      }
      else{
        if(action!=""){
                    return item.events == action;

        }
        else { if(username!="")
                  return item.username == username ;


      }
      if(username=="" && action==""){
      	return true;
      }
      }}
    });
    items.clear();
    items.add(item);
  }

  document.getElementById(\'draw\').onclick = filter;

  var container = document.getElementById(\'visualization\');
  var options = {
  	height: \'600px\',
    editable: false,
    template: function (item) {
    return \'<p style="font-size:10px"><strong>\' + item.username +\'</strong>\'+" - "+  item.events + \'</p>\';
  },
    max:now.clone().add(1,\'day\'),
    start: now.clone().add(-3, \'days\'),
    end: now.clone().add(11, \'days\'),
    zoomMin: 1000 * 60 * 60 ,          // a day
    zoomMax: 1000 * 60 * 60 * 24 * 30 // three months
  };


  var timeline = new vis.Timeline(container, items, options);

  timeline.on(\'select\', function (properties) {
  document.getElementById(\'params\').innerHTML = this.itemsData._data[properties.items[0]].params;
    document.getElementById(\'datetime\').innerHTML = this.itemsData._data[properties.items[0]].start;
    document.getElementById(\'event\').innerHTML = this.itemsData._data[properties.items[0]].events;
  document.getElementById(\'username\').innerHTML = this.itemsData._data[properties.items[0]].username;

});
</script>
<hr>
<h2>Data:</h2>
<div id="data">
<p style="padding-left:1em"><strong>username:</strong></p><p id=\'username\'></p>
<p style="padding-left:1em"><strong>datetime:</strong></p><p id=\'datetime\'></p>
<p style="padding-left:1em"><strong>event:</strong></p><p id=\'event\'></p>
<p style="padding-left:1em"><strong>params:</strong></p><p id=\'params\'></p>

</div>
			';
		return $content ;
	}

	
	
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
