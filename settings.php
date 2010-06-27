<?php
//username and password for the my SQL database
$user="Username";
$password="Password";
//database host
$host="Host";
//database name
$database="Database Name";



//extentions the user is not allowed up upload, it's recommeded that you only add to this list, not subsctract.
$disallowed_extensions = array('perl', 'pl', 'exe', 'asp');

//extentions the user can upload but will have the file type changed to a .txt for securitry reasons, again, just add, don't subscract.
$renamable_extensions = array('php', 'php1', 'php2', 'php3', 'php4', 'php5', 'phtml');

//if it's a photoformat, put it in here
$photo_extensions = array('png', 'jpg', 'jpeg', 'tiff', 'gif', 'jfif', 'exif', 'raw', 'bmp', 'svg');

//if it's a videoformat, put it in here
$video_extensions = array('mp4', 'mov');

//the directory to where all the uploaded files will go, by default it's /uploads, but you can change that is you like. Don't forget to make the direcory that you'll be uploading to first though.
$upload_dir = 'files/';

//maximum file size in megabytes, MUST BE A NUMBER. Do not add anything after the number or before.
$mb = '5';
?>