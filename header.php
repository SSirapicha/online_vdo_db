<?php 

include("config.php");

$type = $_POST['type'];

// Header status message
if ($type == 1) {
    $statusMsg = $_POST['val'];

    // Prepared statement
    $stmt = $conn->prepare("UPDATE header SET statusMsg = ?");
    $stmt->bind_param("s", $statusMsg);

    if ($stmt->execute()) {
        echo $statusMsg;  
    } 
    else {
        die(mysqli_error($conn));
    }
    $stmt->close();
}

// Emojis on horizontal navigation bar
else if ($type == 2) {
    $navEmoji = $_POST['val'];

    // Prepared statement
    $stmt = $conn->prepare("UPDATE header SET emoji = ?");
    $stmt->bind_param("s", $navEmoji);

    if ($stmt->execute()) {
        echo $navEmoji;  
    } 
    else {
        die(mysqli_error($conn));
    }
    $stmt->close();
}

// Header images for slideshow
else if (isset($_POST['saveHeaderImg1'])) {
    // Get uploaded header image
    $filename = $_FILES["headerImg1"]["name"];
    $tempname = $_FILES["headerImg1"]["tmp_name"];
    $folder = "./header/" . $filename;

    // Prepared statement
    $stmt = $conn->prepare("UPDATE header SET img1 = ?");
    $stmt->bind_param("s", $filename);

    // Move the uploaded image into the folder: image
    if (move_uploaded_file($tempname, $folder) && $stmt->execute()) {
        header('location:index.php');   
    } 
    else {
        die(mysqli_error($conn));
    }
    $stmt->close();
}

else if (isset($_POST['saveHeaderImg2'])) {
    $filename = $_FILES["headerImg2"]["name"];
    $tempname = $_FILES["headerImg2"]["tmp_name"];
    $folder = "./header/" . $filename;

    // Prepared statement
    $stmt = $conn->prepare("UPDATE header SET img2 = ?");
    $stmt->bind_param("s", $filename);

    if (move_uploaded_file($tempname, $folder) && $stmt->execute()) {
        header('location:index.php');   
    } 
    else {
        die(mysqli_error($conn));
    }
    $stmt->close();
}

else if (isset($_POST['saveHeaderImg3'])) {
    $filename = $_FILES["headerImg3"]["name"];
    $tempname = $_FILES["headerImg3"]["tmp_name"];
    $folder = "./header/" . $filename;

    // Prepared statement
    $stmt = $conn->prepare("UPDATE header SET img3 = ?");
    $stmt->bind_param("s", $filename);

    if (move_uploaded_file($tempname, $folder) && $stmt->execute()) {
        header('location:index.php');   
    } 
    else {
        die(mysqli_error($conn));
    }
    $stmt->close();
}
 
?>