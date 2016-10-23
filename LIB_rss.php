<?php

function download_parse_rss($target, $filter_array)
    {
    # download tge rss page
    $news = http_get($target, "");
    
    # Parse title & copyright notice
    $rss_array['TITLE'] = return_between($news['FILE'], "<title>", "</title>", EXCL);

    # Parse the items
    $item_array = parse_array($news['FILE'], "<item>", "</item>");
    for($xx=0; $xx<count($item_array); $xx++)
        {
            //for($keyword=0;$keyword<count($filter_array);$keyword ++){
               // if(stristr($item_array[$xx],$filter_array[$keyword])){
                $rss_array['ITITLE'][$xx] = return_between($item_array[$xx], "<title>", "</title>", EXCL);
                $rss_array['ILINK'][$xx] = return_between($item_array[$xx], "<link>", "</link>", EXCL);
                $rss_array['IDESCRIPTION'][$xx] = return_between($item_array[$xx], "<description>", "</description>", EXCL);     
                $rss_array['IPUBDATE'][$xx] = return_between($item_array[$xx], "<pubDate>", "</pubDate>", EXCL);
        }

    return $rss_array;
    }

/***********************************************************************
display_rss_array($rss_array)     						                
-------------------------------------------------------------			
DESCRIPTION:															
		Displays parsed RSS data                                        
INPUT:																    
		$target                                                         
            The web address of the RSS feed                             
RETURNS:																
		Sends results to the display device                             
***********************************************************************/
function display_rss_array($rss_array)
    {
    echo $rss_array['TITLE']."\n";
 
        for($xx=0; $xx<count($rss_array['ITITLE']); $xx++)
            {
                ?>
                <a href="<?php echo $rss_array['ILINK'][$xx]; ?>"><?php echo $rss_array['ITITLE'][$xx]; ?></a><br>
                <?php
                echo $rss_array['IDESCRIPTION'][$xx]."......<br>";
                echo $rss_array['IPUBDATE'][$xx]."<br>";
            }
    } 
?>
