<?php
/**
* This file will listen for post word parameter and 
* will find all synonyms associated with this word and will 
* return as json 
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require_once(dirname(__FILE__).'/utils/utils.php'); // for debug call  debug($msg,$obj)

  require_once ('utils.php');
  include 'letter_index.php';
  $myIndxFile = "th_en_US_new.idx";
  $lines = file($myIndxFile);//file in to an array
  $fdat = fopen('th_en_US_new.dat', 'r');
  
  $replace =($_GET['word'])?$_GET['word']: $_POST['word'];
 
  if (startsWith($replace,'"') || startsWith($replace,"'"))
	  $replace = trim(substr($replace,1,-1));
   //echo "Current word is ".$replace;
  
  if (isset($letters))
			{
				 //echo "\nSearch for ...";
				$searchIndex = strtoupper (substr($replace,0,1));	
				$leter_index = "";
                if (!preg_match('/[^A-Za-z]/', $searchIndex)) // '/[^a-z\d]/i' should also work.				
					$leter_index = $letters[$searchIndex];
				//if ($i <= 44)
				//	error_log( "letter Index is [$leter_index][$searchIndex]"); 
			    // echo "Search for ...";
				if ($leter_index != "" && strlen(trim($leter_index)) > 2)
				{ // we found our index
				  $range = explode("|",$leter_index);
				  $start = $range[0];
				  $end = $range[1];
				 // echo "\n Range from $start to $end ...";
				  for ($j = $start;$j < $end;$j++)
			      {
					  $buffer = "";
					 $pos = strpos($lines[$j], strtolower($replace)."|");
			         //$line_str = substr(strtolower($lines[$j]),0,strlen(strtolower($replace)."|"))
					 // The !== operator can also be used.  Using != would not work as expected
					// because the position of 'a' is 0. The statement (0 != false) evaluates 
					// to false.
					$searchFor = strtolower($replace)."|";
					//if ($pos !== false)				    
					if ( startsWith($lines[$j],$searchFor) )
					{ // we found our word
					   $line_arr = explode("|",$lines[$j]);
					   fseek($fdat, intval($line_arr[1])); // we seek the positon in the big file
					   $buffer = fgets($fdat, 4096); // not so important for only to get the word
					   //if ($i < 20)
						//	echo "We found repace pos = [".$pos."][".$buffer."] at [".$line_arr[1]."] <br/>";
					   break;
					}// end if
				  }// end for($j) 
			   // if ($i <= 44)
				//	error_log( "After the loop [$buffer]"); 
				  if (strlen($buffer) > 0)
				  {
					 //echo "\n We found one ...";
					$replacewith = "";  
				    $syn = "";  
					while (substr(trim($buffer = fgets($fdat, 4096)),0,1) == '(')
					{ 
					   if (strlen($syn) == 0)
							$syn = $buffer;
						else $syn .= "|".$buffer;
						//$buffer = fgets($fp, 4096);
					}//end while
					//print_r(explode('|',$syn));
                    //$json = array('words' => explode('|',$syn));
                    $syn = str_replace('\n', '', $syn); //json_encode($json);
                    //$syn = str_replace(' ', '', $syn);
                    //debug('>>>>>>>>>>>>>>>>>>>>>> DICT.php >>>>>>>>>>>>>>>>> ',$syn);
					echo json_encode( array("word"=>$replace
									,"result"=>$syn));
					//echo $synonyms;
					  
				  }// end if (strlen($buffer) > 0)
					
				} // end if ($leter_index != "" && strlen(trim($leter_index)) > 2)
			}// end if (isset($letters))
         
  die();				
  
?>