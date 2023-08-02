<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" charset="UTF-8" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="seriesDisplay">
        <?php
            include ("config.php");
        
            $query = "select * from series order by id";
            $result = mysqli_query($conn, $query);
        
            while ($data = mysqli_fetch_assoc($result)) {
        ?>
                <!-- Display series container with poster image -->
                <div class="seriesContainer">
                    <div style="background-color:black; border-radius:12px;">
                        <img width="125" height="176" src="./image/<?php echo $data['poster']; ?>" class="seriesPoster">
                        <div class="middle">
                            <!-- Display title and year released of series when hover over series container -->
                            <div class="seriesDescription">
                                <?php echo $data['title']; ?><br>
                                (<?php echo $data['yearReleased']; ?>)
                            </div>

                            <!-- Expand button at the bottom right to display more info in popup -->
                            <a  class="material-symbols-outlined" 
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
                            <span 
                                style="float:right; font-size:25px; cursor:pointer;" 
                                onclick="
                                    document.getElementById('series<?php echo $data['id']; ?>').style.display='none'">&times;
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
                        <button 
                            style="float:right; position:absolute; right:0; bottom:0; margin-right:60px; margin-bottom:18px;"
                            onclick="document.getElementById('deleteSeriesConfirm<?php echo $data['id']; ?>').style.display='block'">Delete
                        </button>

                        <!-- Edit button : display popup for editing series -->
                        <button 
                            style="float:right; position:absolute; right:0; bottom:0; margin-right:20px; margin-bottom:18px;" 
                            onclick="
                                document.getElementById('series<?php echo $data['id']; ?>').style.display='none',
                                document.getElementById('seriesEdit<?php echo $data['id']; ?>').style.display='block'">Edit
                        </button>
                    </div>
                </div>

                <!-- Display popup to confirm deletion -->
                <div id="deleteSeriesConfirm<?php echo $data['id']; ?>" class="deleteSeriesConfirm" style="display:none">
                    <div class="deleteSeriesConfirmPrompt">
                        <p style="float:left; color:white; font-size:15px;">Are you sure you want to delete?</p>

                        <!-- No button : cancel deletion -->
                        <button
                            style="float:right; margin-top:14px;"
                            onclick="document.getElementById('deleteSeriesConfirm<?php echo $data['id']; ?>').style.display='none'">No
                        </button>

                        <form method="post" action="deleteSeries.php">
                            <!-- Yes button : gets id of series to be deleted -->
                            <input type="hidden" name="deleteSeriesId" value="<?php echo $data['id']; ?>">
                            <button 
                                style="float:right; margin-top:14px; margin-right:3px;" 
                                name="deleteSeriesBtn" type="submit">Yes</button>
                        </form>
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
                                <form method="post" action="editSeries.php" enctype="multipart/form-data">
                                    <!-- Get id of series to be edited -->
                                    <input type="hidden" name="editSeriesId" value="<?php echo $data['id']; ?>">
                                    <input type="text" class="editSeriesTitle" name="editTitle" value="<?php echo $data['title']; ?>">
                                    <br><br>

                                    Year:
                                    <input type="number" class="editSeriesYear" name="editYearReleased" value="<?php echo $data['yearReleased']; ?>" 
                                        onKeyPress="if (this.value.length==4) return false;">
                                    <br>

                                    Country:
                                    <select class="editSeriesDropdown" name="editCountry">
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
                                    <select class="editSeriesDropdown" name="editType">
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
                                        if ($notes != null) {
                                    ?>
                                            <!-- If there's notes, display notes -->
                                            <textarea class="editSeriesNotes" name="editNotes" spellcheck="false" maxlength="100"><?php echo $data['notes']; ?></textarea>
                                    
                                    <?php
                                        }
                                        else {
                                    ?>
                                            <!-- If there's no notes, display 'Write something here...'-->
                                            <textarea class="editSeriesNotes" name="editNotes" placeholder="Write something here..." spellcheck="false" maxlength="100"></textarea>
                                    <?php
                                        }
                                    ?>
                                    <br>

                                    <button 
                                        style="float:right; position:absolute; right:0; bottom:0; margin-right:20px; margin-bottom:18px;"
                                        name="editSeriesBtn" type="submit">Save
                                    </button>
                                </form>

                                <button 
                                    style="float:right; position:absolute; right:0; bottom:0; margin-right:60px; margin-bottom:18px;"
                                    onclick="
                                        document.getElementById('series<?php echo $data['id']; ?>').style.display='block'
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
</body>
</html>