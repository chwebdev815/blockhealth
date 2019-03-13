<?php
if($_REQUEST){
file_put_contents('inSMS.txt',json_decode($_REQUEST));
}