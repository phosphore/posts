<html>
<head>
    <title>Posts</title>
    <script src="../libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="../js/CONFIG.js" type="text/javascript"></script>
    <script src="../js/utils.js" type="text/javascript"></script>
    <script src="../js/topic.js" type="text/javascript"></script>
    <script src="../js/ajax_topic.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/common.css" type="text/css" />
    <link rel="stylesheet" href="../css/form.css" type="text/css" />
    <link rel="stylesheet" href="../css/topic.css" type="text/css" />
    <link rel="stylesheet" href="../css/paging.css" type="text/css" />
</head>

<body>

<div id="wrapper">

<div id="form">
    <fieldset>
      <legend>Create a new topic</legend>
      <div id="error_topic" style="display: none"></div>
      <p>
	<label for="author"><span class="required">*</span> Name:</label> 
	<input type="text" name="author" class="text" id="author" />
      </p>
      <p>
	 <label for="title"><span class="required">*</span> Title:</label> 
	 <input type="text" name="title" class="text" id="title" />
      </p>
      <p>
	 <label for="msg"><span class="required">*</span> Message:</label> 
	 <textarea rows="1" cols="1" id="msg" name="msg"></textarea>
      </p>
      <p><input type="button" name="submit" id="submit" value="Post" /></p>
    </fieldset>
</div>

</div>

<div id="content">

  <?php 
    include_once("../inc/CONFIG.inc.php");
    include_once("../class/PDOFactory.class.php");
    include_once("../class/Post.class.php");
    include_once("../class/Topic.class.php");
    include_once("../class/Pager.class.php");	
    include_once("../class/TopicRetrieval.class.php");
    include_once("../class/Utils.class.php");
  ?>
	
<div id="posts"> 
  <?php 
    $topic = new Topic();
    $pager = new Pager();
    $topic_sql = new TopicRetrieval();
					
    $count = $topic_sql->count();
		
    if($count != 0) {	
      $query = $topic_sql->query_topic_with_limit();
      $_topic = $topic_sql->retrieveTopic($query);
		
      for($i = 0; $i < count($_topic); $i++) {
		echo sprintf("<div class=\"topic\" id=\"topic_%s\">
		<a href=\"reply.php?id=%s\"><span class=\"title\">%s</span></a>
		<span class=\"date\">%s</span><span class=\"author_name\">%s</span></div>",
		$_topic[$i]['pk_topic_id'],$_topic[$i]['pk_topic_id'],$_topic[$i]['title'],$topic->format_date($_topic[$i]['timestamp']),$_topic[$i]['author']);
      }
   ?>
</div>
	
<div id="paging">
  <?php
    $total_pages = $topic->total_pages($count); 
		
    $pager->paging($total_pages,1);
    $i = $pager->start_page();
    $pages = $pager->total_pages();
		
    if($pages != 1) {
      while($i <= $pages) {
		if ($i==1) {
	  		echo "<button id=\"curr_pg\" type=\"button\">$i</button>";
		} else {
	  		echo "<button class=\"paging_btns\" type=\"button\" onClick=\"AjaxTopic.paging_topic(1,$i,'" . $topic_sql->getEarliestDate() ."');\">$i</button>";
		}
		$i++;
      }
    }
		
   }
  ?>
</div>
	
<div id="data">
  <?php
    echo "<div id='latest_date' title='" .$topic_sql->getLatestDate() ."'><div>";
    echo "<div id='current_pg' title='1'><div>";
  ?>
</div>

</div>

</body>

</html>
