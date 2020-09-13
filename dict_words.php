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
  if (isset($_GET['word'])){$replace = $_GET['word'];}
  else if (isset($_POST['word'])){$replace =$_POST['word'];}
  else
  {
	 //echo json_encode(array());
	 echo "NO POST<br>";
	 foreach (getallheaders() as $name => $value) {
      echo "$name: $value\n";
     }
	// echo json_encode($_POST);
	 die();
  }
  if (startsWith($replace,'"') || startsWith($replace,"'"))
	  $replace = trim(substr($replace,1,-1));
  
  /*
   * $_POST['word'] pass multiple words and will return 
   * array of all this words
   */
   $replace_tmp = explode(",",$replace);
   $replace_arr = array();
   for ($i = 0;$i < count($replace_tmp);$i++)
   {
	   if(!in_array($replace_tmp[$i], $replace_arr)) 
	  {
		array_push($replace_arr , $replace_tmp[$i]);
	  }
   }
   $result = array();
   //echo "Current word is ".$replace;
  for($i = 0;$i < count($replace_arr);$i++)
  {
  if (isset($letters))
			{
				 $replace = $replace_arr[$i];
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
					//echo json_encode( array("word"=>$replace
					//				,"result"=>$syn));
					//echo $synonyms;
					$obj = new stdClass();
				  if (isset($_POST['single']) || isset($_GET['single']))
                    {
						 $arr_words = explode('|',$syn);
						 $rnd = rand(0,count($arr_words) - 1);
						 while (substr(trim($arr_words[$rnd]),0,1) == '('
						    || (strpos(trim($arr_words[$rnd])," ") !== false
							    || strpos(trim($arr_words[$rnd]),"-") !== false))
						{
							$rnd = rand(0,count($arr_words) - 1);
						}// end while	
                        $syn = trim($arr_words[$rnd]);
						$syn = str_replace('\n','',$syn);
						$syn = str_replace('\\n','',$syn);
						
						$obj->word = $replace;
						$obj->result = $syn;
						array_push($result ,/*array("word"=>$replace
						         			,"result"=>$syn)*/$obj); 						
					}else						
						array_push($result ,array("word"=>$replace
						         			,"result"=>$syn)); 
				  }// end if (strlen($buffer) > 0)
					
				} // end if ($leter_index != "" && strlen(trim($leter_index)) > 2)
			}// end if (isset($letters))
       }//end for()
	//	array_push($result,array("post"=>json_encode($_POST)));
		echo json_encode( $result);
         
  die();				
  
?>