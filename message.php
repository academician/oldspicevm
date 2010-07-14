<?php
// This is the class to generate mp3 files based on the anti-spam words
// Based on the PHP mp3 class at http://www.sourcerally.net/Scripts/20-PHP-MP3-Class
// Output code based on the FPDF class at http://www.fpdf.org
class mp3
  {
  	var $str;
  	var $time;
  	var $frames;

  	// Create a new mp3
  	function mp3($path="")
  	{
  	if($path!="")
  		{
  		$this->str = file_get_contents($path);
  		}
  	}

  // Put an mp3 behind the first mp3
  function mergeBehind($mp3)
  {
  $this->str .= $mp3->str;
  }

  // Calculate where's the end of the sound file
  function getIdvEnd()
  {
  $strlen = strlen($this->str);
  $str = substr($this->str,($strlen-128));
  $str1 = substr($str,0,3);
  if(strtolower($str1) == strtolower('TAG'))
  {
  return $str;
  }
  else
  {
  return false;
  }
  }

  // Calculate where's the beginning of the sound file
  function getStart()
  {
  $strlen = strlen($this->str);
  for($i=0;$i<$strlen;$i++)
  {
  $v = substr($this->str,$i,1);
  $value = ord($v);
  if($value == 255)
  {
  return $i;
  }
  }
  }

  // Remove the ID3 tags
  function striptags()
  {
  //Remove start stuff...
  $newStr = '';
  $s = $start = $this->getStart();
  if($s===false)
  {
  return false;
  }
  else
  {
  $this->str = substr($this->str,$start);
  }
  //Remove end tag stuff
  $end = $this->getIdvEnd();
  if($end!==false)
  {
  $this->str = substr($this->str,0,(strlen($this->str)-129));
  }
  }

  // Display an error
  function error($msg)
  {
  //Fatal error
  die('<strong>audio file error: </strong>'.$msg);
  }

 // Send the new mp3 to the browser
  function output($path)
  {
  //Output mp3
  //Send to standard output
  if(ob_get_contents())
  $this->error('Some data has already been output, can\'t send mp3 file');
  if(php_sapi_name()!='cli')
  {
  //We send to a browser
  header('Content-Type: audio/mpeg3');
  if(headers_sent())
  $this->error('Some data has already been output to browser, can\'t send mp3 file');
  header('Content-Length: '.strlen($this->str));
  header('Content-Disposition: attachment; filename="'.$path.'"');
  }
  echo $this->str;
  return '';
  }
  }
  ?><?
  
  
  // Specify the word
$word = "1579";
$word = "a".$_POST['number']."b".$_POST['opt1']."f".$_POST['opt2'].$_POST['beeps'];

$word_count = strlen($word);

 // Set up the first file
  if ($word_count > 0) {
  $mp3 = new mp3($cas_fontpath . './snippets/' . substr($word, 0, 1) . '.mp3');
  $mp3->striptags();
  }

// Generate the mp3 file from each letter in the word
  for ($i = 1; $i < $word_count; ++$i) {
  $cas_character = $cas_fontpath . 'snippets/' . substr($word, $i, 1);
  $cas_mp3equivalent = new mp3($cas_character . '.mp3');
  $mp3->mergeBehind($cas_mp3equivalent);
  $mp3->striptags();
  }

  // Spit out the audio file!
$mp3->output('message.mp3');
?>