<?php
if($_GET){
file_put_contents('inSMS.txt',json_encode($_GET));
echo "yes";
}