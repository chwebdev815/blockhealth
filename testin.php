<?php
if($_REQUEST){
file_put_contents('inSMS.txt',json_encode($_REQUEST));
}