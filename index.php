<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<?php

include 'getItems.php';
$result = RetrieveItems();

?>

<script>
var RETRY_INTERVAL = 5000;
var customerId = Math.floor((Math.random() * 999) + 1);
var items = <?php echo $result?>;

function loadItems(items){
    if (items.error !== undefined) {
        reloadCatalog();
        return;
    }
    var i = 0;
    console.log("Load Items: " + items.rows);
    document.getElementById("loading").innerHTML = "";
    for(i = 0; i < items.rows.length; ++i){
        addItem(items.rows[i].doc);
    }
}

function reloadCatalog() {
    showErrorMessage("The catalog is not currently available, retrying...");
    window.setTimeout(
        function() {
            $.ajax ({
                type: "GET",
                contentType: "application/json",
                url: "ajaxGetItems.php",
                success: function(result) {
                    loadItems(JSON.parse(result));
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    reloadCatalog();
                } 
            })
        },
        RETRY_INTERVAL
    );
}

function showErrorMessage(message) {
    document.getElementById("loading").innerHTML = message;
}

function addItem(item){
	var div = document.createElement('div');
	div.className = 'item';
	div.innerHTML = "<div class ='well'><img width='100%' height='auto' src = '"+item.imgsrc+"'/><br><button onclick='orderItem(\""+item._id+"\")'><b>Buy</b></button><br><u>"+item.name+"</u><br>"+item.description+"<br><b>$"+item.usaDollarPrice + "</b></div>";
	document.getElementById('boxes').appendChild(div);
}

function orderItem(itemID){
    var count = Math.floor((Math.random() * 99) + 1);
    var myjson = {"itemid": itemID, "customerid": customerId, "count": count};
    
    $.ajax ({
    	type: "POST",
    	contentType: "application/json",
	    url: "submitOrders.php",
	    data: JSON.stringify(myjson),
	    dataType: "json",
	    success: function( result ) {
	        if(result.httpCode != "201" && result.httpCode != "200"){
	        	alert("Failure: check that your JavaOrders API App is running and your user-provided service has the correct URL.");
	        }
	        else{
	        	alert("Order Submitted! Check your Java Orders API to see your orders: \n" + result.ordersURL);
	        }
	    },
	    error: function(XMLHttpRequest, textStatus, errorThrown) { 
	    	alert("Error");
        	console.log("Status: " , textStatus); console.log("Error: " , errorThrown); 
    }  
	});

}

</script>
<html>
<head>
	<title>Microservices Sample</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<link rel="stylesheet" href="style.css">
</head>
<table class="headerTable">
	<tr>
		<td><span class="pageTitle"><h1>Microservices Sample</h1></span></td> 
	</tr>
</table>
<body onload="loadItems(items)">
	<div class="container">
		<div id='boxes' class="notes"></div>
	</div>
	<div id="loading"><br>Loading...</div>
	<a href="./autoLoadTest.html">Catalog Load Tester</a>

    <!-- Add large images, uncomment the next img lines to create a response time alarm in BAM -->
    <!--
    <img id="berlin" src="http://upload.wikimedia.org/wikipedia/commons/2/26/Invalidenpark%2C_Berlin-Mitte%2C_360x180%2C_160326%2C_ako.jpg" height="25%" width="25%" alt="Oops">
    <img id="berlin2" src="http://upload.wikimedia.org/wikipedia/commons/2/26/Invalidenpark%2C_Berlin-Mitte%2C_360x180%2C_160326%2C_ako.jpg" height="25%" width="25%" alt="Oops">
    <img id="ubeck" src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/%D0%9E%D0%B3%D0%BE%D0%BD%D0%B5%D0%BA_1917-32.pdf/page1-1285px-%D0%9E%D0%B3%D0%BE%D0%BD%D0%B5%D0%BA_1917-32.pdf.jpg" height="25%" width="25%" alt="Oops">
    <img id="bottles" src="http://upload.wikimedia.org/wikipedia/commons/0/07/Multi-use_water_bottle.JPG" height="25%" width="25%" alt="Oops">
    <img id="pong" src="http://upload.wikimedia.org/wikipedia/commons/b/b6/Mesa_de_ping_pong%2C_Riesstr.%2C_M%C3%BAnich%2C_Alemania_2012-04-28%2C_DD_01.JPG"  height="25%" width="25%" alt="Oops">
    <img id="portugal" src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Praia_da_Rocha-Portimao_%28Portugal%29_%2833512845665%29.jpg" height="25%" width="25%" alt="Oops">
    -->
    <!-- Add large images, uncomment the previous img lines to create a response time alarm in BAM --> 
     
</body>
</html>

