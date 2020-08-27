<?php 

//*********************** Functions ************************************//
function startsWith($haystack, $needle) 
{
    // search backwards starting from haystack length characters from the end
    return ($needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE);
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


function inserthml($artcl,$searchFor,$replWith)
{
    $tmp = "";
	$startPos = 0;
    $posTag = strpos($artcl, '>', 1);	
	while ($posTag > 0)
	{
		$posEndTag = strpos($artcl, '<', $posTag + 1);
        $tmp .= substr($artcl,$startPos,$posTag - $startPos);
        $tmpStr = substr($artcl,$posTag,$posEndTag - $posTag);
        $tmpStr = str_replace($searchFor,$replWith,$tmpStr);
		$tmp .= $tmpStr;
		$startPos = $posEndTag + 1;
		$posTag = strpos($artcl, '>', $startPos);	
		if ($posTag < 1)
		{ // append the rest of the 
		  $tmp .= substr($artcl,$startPos);
		}
	}
	return tmp;
}
?>