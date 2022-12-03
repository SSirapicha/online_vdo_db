<?php 
include("config.php");

// Prepared Statement
$sql = "INSERT INTO video_provider (provider_url) VALUES (?)";

if ($stmt = mysqli_prepare($conn, $sql)) {
	// Bind variables to the prepared statement as parameters
	mysqli_stmt_bind_param($stmt, "s", $provider_url);

	// Set parameter
	$provider_url = $_REQUEST['url'];

	// Execute the prepared statement
	if (mysqli_stmt_execute($stmt)) {
		echo '<center><b>Successfully Inserted!<b></center><br>';
	}
	else {
		echo '<center><b>Fail To Insert!<b></center><br>';
	}
}

// Display table with all columns
$query = "SELECT * FROM video_provider";

echo   '<center>
		<table style="border: 1px solid black;"> 
      		<tr> 
			<th style="border: 1px solid black;">Online Video Provider URL</th>
        		<th style="border: 1px solid black;">Name</th>
      		</tr>';

if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $url = $row["provider_url"];
        $name = $row["provider_name"];

        echo 	'<tr> 
                  	<td style="border: 1px solid black;">'.$url.'</td> 
                  	<td style="border: 1px solid black;">'.$name.'</td> 
              	</tr>';
    }
    $result->free();
} 

// Close statement
mysqli_stmt_close($stmt);
 
// Close connection
mysqli_close($conn);

echo '</table>';

// Button to go back to main page
echo '<br><a href="index.php"><button>Back</button></a></center>';
?>
