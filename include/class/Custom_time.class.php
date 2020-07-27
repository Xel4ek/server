<?php
class Custom_time {
private $secs;
public function __construct($secs){
    $this->secs = $secs;
}
   
    private function num_word($value, $words, $show = true) 
{
	$num = $value % 100;
	if ($num > 19) { 
		$num = $num % 10; 
	}
	
	$out = ($show) ?  $value . ' ' : '';
	switch ($num) {
		case 1:  $out .= $words[0]; break;
		case 2: 
		case 3: 
		case 4:  $out .= $words[1]; break;
		default: $out .= $words[2]; break;
	}
	
	return $out;
}
 
public function __toString(){

	$res = '';
	$secs = $this->secs;
	$days = floor($secs / 86400);
	$secs = $secs % 86400;
	if ($days != 0) {
	$res .= $days . 'д. ';
	}
	$hours = floor($secs / 3600);
	$secs = $secs % 3600;
	if ($hours != 0) {
	$res .= $hours . 'ч. ';
 }
	$minutes = floor($secs / 60);
	$secs = $secs % 60;
	if($minutes != 0) {
	$res .= $minutes . 'м. ' ;
 }
	$res .= $secs . 'с.';
	
	return $res;
}
}
