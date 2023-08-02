<!DOCTYPE html>
<html>

<head>
    <title>welcome</title>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial-scale=1">
    
    <!-- 90's font , VT323 ; 'font-family: 'VT323', monospace;' -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

    <!-- Symbol -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!-- Css, JavaScript, jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
        include ("config.php");

        $query = "select img1, img2, img3, statusMsg, emoji from header";
        $result = mysqli_query($conn, $query);

        $data = mysqli_fetch_assoc($result);
    ?>

    <!-- Header text, status & image slideshow -->
    <header>
        <div class="headerContainer">
            <!-- Header text -->
            <div class="headerText">
                <h2>
                    Welcome, ...<br>
                    
                    <!-- Update status bar message by clicking outside of the bar or press enter -->
                    <input type="text" class="statusBar" id="statusMsg" name="statusMsg" onchange="statusMsgUpdate()" placeholder='Enter status message...' value="<?php error_reporting(E_ALL ^ E_WARNING); echo $data['statusMsg']; ?>" maxlength="45">
                </h2>
            </div>

            <!-- Image slideshow -->
            <div class="slideShowImg fade">
                <img src="./header/<?php echo $data['img1']; ?>" width="500" height="100">
            </div>
            <div class="slideShowImg fade">
                <img src="./header/<?php echo $data['img2']; ?>" width="500" height="100">
            </div>
            <div class="slideShowImg fade">
                <img src="./header/<?php echo $data['img3']; ?>" width="500" height="100">
            </div>
        </div>
    </header>

    <section>
        <!-- Horizontal navigation bar with logout button and username button -->
        <div class="horNavBar">
            <div class="logout">
                <button>log-out</button>
            </div>
            <div class="username">
                <button>username</button>
            </div>
            <div id="displayHorNavEmoji" style="float:right; padding-right:6px; padding-top:3px; font-size:12px;"><?php echo $data['emoji']; ?></div>
        </div>

        <!-- Vertical navigation bar -->
        <div id="verNavBar" class="verNavBar">
            <button onclick="navContent('headerNav')">&#128252 Header</button><br>
            <button onclick="navContent('seriesNav'); 
                    document.getElementById('seriesList').style.display='block';">&#127916 Series
            </button><br>
            <button onclick="navContent('musicNav')">🎸 Music</button><br>
        </div>

        <div id="headerNav" class="navContent" style="display:block;">
            <br>
            Emoji: 
            <input type="text" class="navEmoji" id="navEmoji" name="navEmoji" onchange="emojiUpdate()" value="<?php echo $data['emoji']; ?>" maxlength="20">

            <script>
                $('input').keyup(function() {
                    var emj = $('#navEmoji').val();
                    $('#displayHorNavEmoji').text(emj); 
                });
            </script>
            <br><br>

            <form method="post" action="header.php" enctype="multipart/form-data">
                <label for="headerImg1">Header 1</label>
                <input type="file" accept="image/*" id="headerImg1" name="headerImg1">
                <input type="submit" name="saveHeaderImg1" value="Save">
            </form>
            <img src="./header/<?php echo $data['img1']; ?>" width="300" height="80">

            <form method="post" action="header.php" enctype="multipart/form-data">
                <label for="headerImg2">Header 2</label>
                <input type="file" accept="image/*" id="headerImg2" name="headerImg2">
                <input type="submit" name="saveHeaderImg2" value="Save">
            </form>
            <img src="./header/<?php echo $data['img2']; ?>" width="300" height="80">

            <form method="post" action="header.php" enctype="multipart/form-data">
                <label for="headerImg3">Header 3</label>
                <input type="file" accept="image/*" id="headerImg3" name="headerImg3">
                <input type="submit" name="saveHeaderImg3" value="Save">
            </form>
            <img src="./header/<?php echo $data['img3']; ?>" width="300" height="80">
        </div>

        <!-- Series navigation -->
        <div id="seriesNav" class="navContent" style="display:none;">
            <h1 style="font-size:25px;">Series</h1>
            <button class="seriesNavBtn" onclick="seriesContent('seriesList');">List</button> |
            <button class="seriesNavBtn" onclick="seriesContent('seriesNew')">New</button>
        </div>

        <!-- Music navigation -->
        <div id="musicNav" class="navContent" style="display:none;">
            <h1 style="font-size:25px;">Music</h1>
        </div>

        <!-- Display series from database -->
        <div id="seriesList" class="seriesContent" style="display:none;">
            <div id="seriesDisplay" class="seriesDisplay" style="padding-top:8px;">
                <?php
                    $query = "select * from series order by id";
                    $result = mysqli_query($conn, $query);
                
                    while ($data = mysqli_fetch_assoc($result)) {
                ?>
                        <!-- Display series container with poster image -->
                        <div class="seriesContainer">
                            <div style="background-color:black; border-radius:12px;">
                                <img width="125" height="176" src="./image/<?php echo $data['poster']; ?>" class="seriesPoster">
                                <!-- Display title and year released of series when hover over series container -->
                                <div class="middle">
                                    <div class="seriesDescription">
                                        <?php echo $data['title']; ?><br>
                                        (<?php echo $data['yearReleased']; ?>)
                                    </div>

                                    <!-- Expand button at the bottom right to display more info in popup -->
                                    <a class="material-symbols-outlined" 
                                       style="cursor:pointer; font-size:18px; color:white; position:absolute; right:0; bottom:0;" 
                                       onclick="document.getElementById('series<?php echo $data['id']; ?>').style.display='block';">expand_content
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Display series details in popup -->
                        <div id="series<?php echo $data['id']; ?>" class="seriesPopup" style="display:none;">
                            <div class="seriesPopupContainer">
                                <!-- 'X' : to close popup -->
                                <div class="seriesPopupHeader">
                                    <span style="float:right; font-size:25px; cursor:pointer;" 
                                          onclick="document.getElementById('series<?php echo $data['id']; ?>').style.display='none'">&times;
                                    </span>
                                </div>

                                <!-- Series details -->
                                <div class="seriesPopupContent">
                                    <div style="float:left;">
                                        <img width="160" height="211" src="./image/<?php echo $data['poster']; ?>" class="seriesPoster">
                                    </div>
                        
                                    <div style="float:left; padding-top:10px; padding-bottom:20px; padding-left:20px;">
                                        <div style="width:190px;"><?php echo $data['title']; ?></div><br>
                                        Year: <?php echo $data['yearReleased']; ?><br>
                                        Country: <?php echo $data['country']; ?><br>
                                        Type: <?php echo $data['type']; ?>

                                        <?php
                                            // If there's a notes, display notes
                                            $notes = $data['notes'];
                                            if ($notes != null) {
                                                echo '<br><br>&#128221:<br>';
                                                echo '<div style="width:190px;">';
                                                echo $notes;
                                                echo '</div>';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                
                                <!-- Delete button : display popup to confirm deletion -->
                                <button style="float:right; position:absolute; right:0; bottom:0; margin-right:60px; margin-bottom:18px;"
                                        onclick="document.getElementById('deleteSeriesConfirm<?php echo $data['id']; ?>').style.display='block'">Delete
                                </button>

                                <!-- Edit button : display popup for editing series -->
                                <button style="float:right; position:absolute; right:0; bottom:0; margin-right:20px; margin-bottom:18px;" 
                                        onclick="document.getElementById('series<?php echo $data['id']; ?>').style.display='none',
                                                 document.getElementById('seriesEdit<?php echo $data['id']; ?>').style.display='block'">Edit
                                </button>
                            </div>
                        </div>

                        <!-- Display popup to confirm deletion -->
                        <div id="deleteSeriesConfirm<?php echo $data['id']; ?>" class="deleteSeriesConfirm" style="display:none">
                            <div class="deleteSeriesConfirmPrompt">
                                <p style="float:left; color:white; font-size:15px; padding-left:9px;">Are you sure you want to delete?</p>

                                <!-- No button : cancel deletion -->
                                <button style="float:right; margin-top:14px;"
                                        onclick="document.getElementById('deleteSeriesConfirm<?php echo $data['id']; ?>').style.display='none'">No
                                </button>

                                <button id="deleteSeriesId<?php echo $data['id']; ?>" name="deleteSeriesId" style="float:right; margin-top:14px; margin-right:3px;" 
                                        onclick="deleteSeries(<?php echo $data['id']; ?>)">Yes
                                </button>
                            </div>
                        </div>

                        <!-- Display popup to edit series -->
                        <div id="seriesEdit<?php echo $data['id']; ?>" class="seriesPopup" style="display:none;">
                            <div class="seriesPopupContainer">
                                <div class="seriesPopupHeader">
                                    <!-- 'X' : to close popup -->
                                    <span style="float:right; font-size:25px; cursor:pointer;" 
                                          onclick="document.getElementById('seriesEdit<?php echo $data['id']; ?>').style.display='none'">&times;
                                    </span>
                                </div>
                                
                                <!-- Display current details on popup to edit -->
                                <div class="seriesPopupContent">
                                    <div style="float:left;">
                                        <img width="160" height="211" src="./image/<?php echo $data['poster']; ?>" class="seriesPoster">
                                    </div>
                                    
                                    <div style="float:left; padding-top:10px; padding-bottom:20px; padding-left:20px;">
                                        <input type="text" class="editSeriesTitle" id="editTitle<?php echo $data['id']; ?>" name="editTitle" value="<?php echo $data['title']; ?>">
                                        <br><br>

                                        Year:
                                        <input type="number" class="editSeriesYear" id="editYearReleased<?php echo $data['id']; ?>" name="editYearReleased" value="<?php echo $data['yearReleased']; ?>" 
                                               onKeyPress="if (this.value.length==4) return false;">
                                        <br>

                                        Country:
                                        <select class="editSeriesDropdown" id="editCountry<?php echo $data['id']; ?>" name="editCountry">
                                            <option value="<?php echo $data['country']; ?>" selected><?php echo $data['country']; ?></option>
                                            <option value="China">China</option>
                                            <option value="Japan">Japan</option>
                                            <option value="South Korea">South Korea</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="USA">USA</option>
                                        </select>
                                        <br>

                                        Type:
                                        <select class="editSeriesDropdown" id="editType<?php echo $data['id']; ?>" name="editType">
                                            <option value="<?php echo $data['type']; ?>" selected><?php echo $data['type']; ?></option>
                                            <option value="Drama">Drama</option>
                                            <option value="Film">Film</option>
                                            <option value="Movie">Movie</option>
                                            <option value="Series">Series</option>
                                            <option value="TV Show">TV Show</option>
                                        </select>
                                        <br>

                                        <?php
                                            $notes = $data['notes'];
                                            echo '<br>&#128221:<br>';
                                            if ($notes != null) { ?>
                                                <!-- If there's notes, display notes -->
                                                <textarea class="editSeriesNotes" id="editNotes<?php echo $data['id']; ?>" name="editNotes" spellcheck="false" maxlength="100"><?php echo $data['notes']; ?></textarea>
                                        <?php } else { ?>
                                                <!-- If there's no notes, display 'Write something here...'-->
                                                <textarea class="editSeriesNotes" id="editNotes<?php echo $data['id']; ?>" name="editNotes" placeholder="Write something here..." spellcheck="false" maxlength="100"></textarea>
                                        <?php } ?>
                                        <br>

                                        <button id="editSeriesId<?php echo $data['id']; ?>" name="editSeriesId" style="float:right; position:absolute; right:0; bottom:0; margin-right:20px; margin-bottom:18px;"
                                                onclick="editSeries(<?php echo $data['id']; ?>)">Save
                                        </button>

                                        <button style="float:right; position:absolute; right:0; bottom:0; margin-right:60px; margin-bottom:18px;"
                                                onclick="document.getElementById('series<?php echo $data['id']; ?>').style.display='block'
                                                         document.getElementById('seriesEdit<?php echo $data['id']; ?>').style.display='none'">Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php 
                    } 
                ?>
            </div> 
        </div>

        <!-- Display form to add new series -->
        <div id="seriesNew" class="seriesContent" style="display:none;">
            <form id="newSeriesForm" class="newSeries" action="newSeries.php" enctype="multipart/form-data">
                <!-- Upload series poster image -->
                <div class="posterContainer">
                    <span class="drop-zone__prompt" style="background-color:inherit;">Click to upload</span>
                    <input type="file" accept="image/*" name="posterImage" class="drop-zone__input" required>
                </div>

                <!-- Enter title of the series -->
                <div class="newSeriesForm">
                    <label class="newSeriesLabel" for="title">Title<span style="color:red;">*</span></label>
                    <input type="text" class="newSeriesTitle" name="title" placeholder="Title" required>
                </div>

                <!-- Enter year released of the series -->
                <div class="newSeriesForm">
                    <label class="newSeriesLabel" for="year">Year released<span style="color:red;">*</span></label>
                    <input type="number" class="newSeriesYear" name="yearReleased" placeholder="Format: YYYY" onKeyPress="if (this.value.length==4) return false;" required>
                </div>

                <!-- Enter country of the series -->
                <div class="newSeriesForm">
                    <label class="newSeriesLabel" for="country">Country<span style="color:red;">*</span></label>
                    <select class="newSeriesDropdown" name="country" required>
                        <option> </option>
                        <option value="China">China</option>
                        <option value="Japan">Japan</option>
                        <option value="South Korea">South Korea</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Thailand">Thailand</option>
                        <option value="USA">USA</option>
                    </select>
                </div>

                <!-- Enter type of the series -->
                <div class="newSeriesForm">
                    <label class="newSeriesLabel" for="type">Type<span style="color:red;">*</span></label>
                    <select class="newSeriesDropdown" name="type" required>
                        <option> </option>
                        <option value="Drama">Drama</option>
                        <option value="Film">Film</option>
                        <option value="Movie">Movie</option>
                        <option value="Series">Series</option>
                        <option value="TV Show">TV Show</option>
                    </select>
                </div>

                <!-- Enter notes for series -->
                <div class="newSeriesForm">
                    <label class="newSeriesLabel" for="notes">Notes:</label><br>
                    <textarea class="newSeriesTextarea" name="notes" rows="4" placeholder="Write something here..." spellcheck="false" maxlength="100"></textarea>
                </div>

                <input type="reset" name="reset" value="Reset">
                <input type="submit" value="Save">
            </form>
        </div>
    </section>

    <script>
        // Display content from vertical navigation
        function navContent(navName) {
            var i, navNone, seriesNone;
            // Display none from vertical navigation
            navNone = document.getElementsByClassName("navContent");
            // Display none from series navigation
            seriesNone = document.getElementsByClassName("seriesContent");
            for (i = 0; i < navNone.length; i++) {
                navNone[i].style.display = "none";
            }
            for (i = 0; i < seriesNone.length; i++) {
                seriesNone[i].style.display = "none";
            }
            // Display selected vertical navigation content
            document.getElementById(navName).style.display = "block";
        }

        // Display content from series
        function seriesContent(navName) {
            var i, seriesNone;
            // Display none from series navigation
            seriesNone = document.getElementsByClassName("seriesContent");
            for (i = 0; i < seriesNone.length; i++) {
                seriesNone[i].style.display = "none";
            }
            // Display selected series content
            document.getElementById(navName).style.display = "block";
        }

        /* 
        // Display content from php file to div html
        function displayContent(htmlDiv, phpFile) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById(htmlDiv).innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", phpFile, true);
            xhttp.send();
        }
        displayContent("displaySeries", "displaySeries.php");
        displayContent("displayMusic", "displayMusic.php");
        $('#displaySeries').load('displaySeries.php');
        */

        // Display slideshow images
        let slideIndex = 0;
        displaySlideShow();
        function displaySlideShow() {
            let i;
            let slides = document.getElementsByClassName("slideShowImg");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slideIndex++;
            if (slideIndex > slides.length) { 
                slideIndex = 1 
            }    
            slides[slideIndex-1].style.display = "block";  
            setTimeout(displaySlideShow, 5000);
        }

        function statusMsgUpdate() {
            var v = $('#statusMsg').val(); 
            $('#statusMsg').load('header.php', {type:1, val:v});
        }

        function emojiUpdate() {
            var v = $('#navEmoji').val(); 
            $('#displayHorNavEmoji').load('header.php', {type:2, val:v});
        }

        $('#statusMsg').keydown(function(e) {
            if (e.key === "Enter") {
                statusMsgUpdate();
            }
        })

        $('#navEmoji').keydown(function(e) {
            if (e.key === "Enter") {
                emojiUpdate();
            }
        })

        $("#newSeriesForm").on("submit", function(e) {
            e.preventDefault();

            var form = $(this)[0];
            var formData = new FormData(form);
            var actionUrl = $(this).attr("action");

            $.ajax({
                url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                
                success: function(data) {
                    $("#seriesDisplay").html(data);

                    seriesContent('seriesList');
                    $('#seriesList').load(' #seriesDisplay');
                },
                error: function() {}
            });
        });

        function deleteSeries(id) {
            $('#seriesDisplay').load('seriesProcess.php', {type:1, val:id});
        }

        function editSeries(id) {
            var t = $('#editTitle'+id).val(); 
            var y = $('#editYearReleased'+id).val(); 
            var c = $('#editCountry'+id).val(); 
            var p = $('#editType'+id).val(); 
            var n = $('#editNotes'+id).val(); 

            $('#seriesDisplay').load('seriesProcess.php', {type:2, val:id, editTitle:t, editYearReleased:y, editCountry:c, editNotes:n, editType:p});
        }
        

        // Get emoji input and display it in the header
        function headerEmoji() {
            var emoji = document.getElementById('navEmoji').value;
            document.getElementById('displayHorNavEmoji').innerHTML = emoji;
        }
        
        // drop image on new series form need review
        document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
	        const dropZoneElement = inputElement.closest(".posterContainer");

	        dropZoneElement.addEventListener("click", (e) => {
		        inputElement.click();
	        });

	        inputElement.addEventListener("change", (e) => {
		        if (inputElement.files.length) {
			        updateThumbnail(dropZoneElement, inputElement.files[0]);
		        }
	        });

	        dropZoneElement.addEventListener("dragover", (e) => {
		        e.preventDefault();
		        dropZoneElement.classList.add("drop-zone--over");
	        });

	        ["dragleave", "dragend"].forEach((type) => {
		        dropZoneElement.addEventListener(type, (e) => {
			        dropZoneElement.classList.remove("drop-zone--over");
		        });
	        });

	        dropZoneElement.addEventListener("drop", (e) => {
		        e.preventDefault();

		        if (e.dataTransfer.files.length) {
			        inputElement.files = e.dataTransfer.files;
			        updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
		        }

		    dropZoneElement.classList.remove("drop-zone--over");
	        });
        });

        /* Update thumbnail */
        function updateThumbnail(dropZoneElement, file) {
            let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

            // First time - remove the prompt
            if (dropZoneElement.querySelector(".drop-zone__prompt")) {
                dropZoneElement.querySelector(".drop-zone__prompt").remove();
            }

            // First time - there is no thumbnail element, so lets create it
            if (!thumbnailElement) {
                thumbnailElement = document.createElement("div");
                thumbnailElement.classList.add("drop-zone__thumb");
                dropZoneElement.appendChild(thumbnailElement);
            }

            thumbnailElement.dataset.label = file.name;

            // Show thumbnail for image files
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();

                reader.readAsDataURL(file);
                reader.onload = () => {
                    thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
                };
            } 
            else {
                thumbnailElement.style.backgroundImage = null;
            }
        }
    </script>
</body>