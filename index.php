<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}
?>
<!DOCTYPE html>
<html  xmlns="http://www.w3.org/1999/xhtml">
	
	<head>
		<title>Database demo</title>
		<meta name="viewport" content="width=device-width, initial scale=1">
		
		<style>
			#heading{color:white; text-shadow: 1px 1px 4px blue;}
			#tr{ border-bottom: 2px solid black; width:280px;}
			#container{ 
				overflow: auto; 
				background-color: #A5FFE8; 
				font-size: 18px; 
				width: 300px; 
				padding: 5px; 
				margin: 0; 
				box-shadow: 1px 1px 4px #51C6FF;  
				text-shadow: 1px 1px 2px #BEF4BB; 
				height:55vh;
				scroll-behavior: smooth;
			}
			#textbox {
				 width:230px; 
				 height: 60px; 
				 color: #3B2A00; 
				 padding: 5px; 
				 margin: 0; 
				 box-shadow: 1px 1px 4px blue; 
				 font-size:18px
			}
			#sendbtn{ 
				border-radius: 50%; 
				box-shadow: 1px 1px 4px #37FF65; 
				color: #2C773E; 
				font-weight: 900; 
				height: 60px;
				width:60px; 
				padding: 5px; 
				margin: 0; 
				font-size:18; 
				background-color: white; 
				
				
			}
			#sendlo{ 
				margin: 5px auto 5px auto; 
				padding: 5px; 
				width: 330px ; 
				box-shadow: 1px 1px 4px blue;
				
				position: absolute;
				bottom: 0;
				z-index:-10;
			
			
			}
			body{ background-color:black;}
			#text { color: #65002C;}
			#txtc { background-color : #FDFFA9; padding: 4px;margin:2px; border-radius: 10px; border-bottom: solid 2px #832E53;display: inline-block;}
			
			#username{
				
				background-color: black;
				width:100pvh;
				height: 100vh;
				padding : 20px;
			
			}
		</style>
	</head>
	
	<body>
		
		<?php
		session_start();
		
		if(!isset($_SESSION['username'])){
			echo '<div id="username">';
			echo "<h3 style='color:white;'>please insert username</h3>";
			echo "<form method='post' action='data.php' >";
			echo "<input type='text' name='user' placeholder='username'>";
			echo "<input type='submit' value='enter'>";
			echo "</form>";
			echo '/div>';

			$_SESSION['username'] = $_POST['user'];
			if(isset($_SESSION['username'])){

				header("Refresh:0");
			}
		}else{
		$write = 1;
         $dbhost = 'localhost';
         $dbuser = 'mujtaba';
         $dbpass = 'Mybro.12';
         $db = 'gm';
         
         $idi = 0;
         
         $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
         if(! $conn ) {
			// echo "cant connect";
            die('Could not connect: ' . mysql_error());
         }
       //  echo 'Connected successfully';
         
         if(isset($_POST['name']) && isset($_POST['query'])){
			 $name = '<div id="txtc"><sup><b>'.$_SESSION['username'].'</b></sup> : <span id="text">'.$_POST['name'].'</span></div>' ;
			 $id = time();
			 $date = date("Y-m-d");
			 $isql = "insert into std (name, id, date) values ( '$name', $id, now() ); "; //query for inserting data
			 if(mysqli_query($conn, $isql)){
				 //echo "successfully inserted";
				 unset($_POST['query']);
			
			 }else{
				 echo "Error: " . $isql . "<br>" . mysqli_error($conn);
			 } 
		 }
		 
		 //===========================================================
		 
		 if(isset($_POST['rembtn'])){			//cleaning chat
			
			$removequery = "truncate table std";
			
			if(mysqli_query($conn, $removequery)){
				//echo "removed successfully:";
				
				$dom = new DOMDocument();
				$dom->encoding = 'utf-8';
				$dom->xmlVersion = '1.0';
				$dom->formatOutput = true;
				$xml_file_name = '/var/www/html/images/std_list.xml';
				$root = $dom->createElement('Students');
				$dom->appendChild($root);
				$dom->save($xml_file_name);
		
			}else{
				echo "  could not remove " . $removequery . "<br>" . mysqli_error($conn);
			}
		}
       //================================================================================================================
         $sql = "select * from std;";  // query for display all
         $result = mysqli_query($conn, $sql);
         
        if (mysqli_num_rows($result) > 0) {
			//creating xml
			$dom = new DOMDocument();
			$dom->encoding = 'utf-8';
			$dom->xmlVersion = '1.0';
			$dom->formatOutput = true;
			$xml_file_name = '/var/www/html/images/std_list.xml';
			$root = $dom->createElement('Students');
		
			while($row = mysqli_fetch_assoc($result)) {
				
				//==============================================================
				$student_node = $dom->createElement('Student');
				$child_node_name = $dom->createElement('name', $row['name']);
				$student_node->appendChild($child_node_name);
				$child_node_id = $dom->createElement('id', $row['id']);
				$student_node->appendChild($child_node_id);
				$root->appendChild($student_node);
			}
			
			$dom->appendChild($root);
			$dom->save($xml_file_name);
				
			$_POST['changed'] = "0";
		} else {
		//echo "0 results";
		}
		
		//======== deleting file

         mysqli_close($conn);   
         //$write=0;
  
	 }
      ?>
      
      <script>
		var len=0;
		var i=0;
		
		function sentmsg(){
				
					var data = new FormData();
					data.append("name", document.getElementById("textbox").value);
					document.getElementById("textbox").value=null;
					
					data.append("query", "1");			
			
					var xhr = new XMLHttpRequest(); 
					xhr.open("POST", "data.php");
					xhr.onload= function(){
						console.log(this.response);
					};
				//	do{
					//var resent = <?php echo $write; ?>;
					
					//}while(resent == 1);
					xhr.send(data);
				
					return false;
			}
			
		function loadDoc() {
			var xhttp = new XMLHttpRequest();
			
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					
					len = this.responseXML.getElementsByTagName("name").length;
				
					//==========================================================
					
					if(len==0 && i!=0){
						
						window.location.replace("data.php");
					}
			
					if(i<len){
						var table = document.createElement("table");
						table.id="table1";		
						
					while (i < len){
						
						var row = document.createElement("tr");
						var data = document.createElement("td");
						data.classList.add("data1");
						data.innerHTML = this.responseXML.getElementsByTagName("name")[i].childNodes[0].nodeValue;
						row.appendChild(data);
						table.appendChild(row);				
						i++;	
					}
					document.getElementById("container").appendChild(table); 
					scrolldown();
				}
				
			//==================================================
					
					
				}
			};		

			xhttp.open("POST", "images/std_list.xml", true);
	
			
			//	if(<?php echo $_POST['changed'];?> == "1")
				xhttp.send();
				setTimeout(loadDoc,500);
			
		}
		function scrolldown(){
			
			document.getElementById('container').scrollTop = document.getElementById('container').scrollHeight;
		}
		loadDoc();	
	
		</script>

      <div id="sendlo">
		   <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<input type="submit" value="clear chat" name="rembtn">
			</form>  
			<h1 id="heading">Simple chat Box</h1>
			<div id="container">
			</div>
    
			<form id="bottompart" onsubmit="return sentmsg()">
					<input id="textbox" type="text" name="name" placeholder="message" autocomplete="off" onclick="setTimeout(scrolldown,250);">
					<input id="sendbtn" type="submit" name="query" value="&gt">
			</form>
		
	</div>		
	
	
	</body>
	
</html>
