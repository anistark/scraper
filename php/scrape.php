<?php
include_once('simple_html_dom.php');

$count = 0;

function scraping_stackoverflow() {
    // create HTML DOM
    for ($i = 1; $i <= 5; $i++) {
        $url[i]='http://stackoverflow.com/questions/tagged/'.$_POST['value'.$i];
        $html = file_get_html($url[i]);

        // get questions block
        $c=0;
        $nitem['tag'] = $_POST['value'.$i];
        foreach($html->find('div[class^=question-summary]') as $questions) {
            // get question url
            $item['question'] = trim($questions->find('a.question-hyperlink', 0)->plaintext);
            $qurl = trim($questions->find('a.question-hyperlink', 0)->href);
            $qhtml = file_get_html('http://stackoverflow.com'.$qurl);
            foreach($qhtml->find('div[id^=answers]') as $ans) {
                $ac = trim($ans->find('div.accepted-answer', 0)->id);
                if($ac != null) {
                    $c = $c + 1;
                }
            }
            // get question vote
            $item['vote'] = trim($questions->find('span.vote-count-post', 0)->plaintext);
            // get status
            $item['status'] = trim($questions->find('div.status', 0)->plaintext);
            $item['status'] = preg_replace("/[^0-9,.]/", "", $item['status']);
            // get views
            $item['views'] = trim($questions->find('div.views', 0)->plaintext);
            $item['views'] = preg_replace("/[^0-9,.]/", "", $item['views']);
            $ret[] = $item;
        }
        $nitem['accpeted_answers'] = $c;
        $nret[] = $nitem;
        
        // clean up memory
        $html->clear();
        unset($html);
    }
    return $nret;
}

// -----------------------------------------------------------------------------
// test it!
$nret = scraping_stackoverflow();

/*foreach($nret as $nv) {
    echo $nv['tag'];
    echo $nv['accepted_answers'];
}*/

// json part
$file = file_get_contents('data.json');
$data = json_decode($file);
unset($file);//prevent memory leaks for large json.
//insert data here
$data[] = array('data'=>'scraper data');
//save the file
file_put_contents('data.json',json_encode($nret));
unset($data);//release memory

$file = file_get_contents('data.json');
$json_array  = json_decode($file, true);
unset($file);

// json to csv
$json = file_get_contents('data.json');
$array = json_decode($json, true);
$f = fopen('data.csv', 'w');
$firstLineKeys = false;
foreach ($array as $line)
{
	if (empty($firstLineKeys))
	{
		$firstLineKeys = array_keys($line);
		fputcsv($f, $firstLineKeys);
		$firstLineKeys = array_flip($firstLineKeys);
	}
	// Using array_merge is important to maintain the order of keys acording to the first element
	fputcsv($f, array_merge($firstLineKeys, $line));
}


$elementCount  = count($json_array);

// Display
$answered_count = $elementCount - $count;
//echo 'Total questions: '.$elementCount.'<br />';
//echo 'Total Answered questions: '.$answered_count;

header("Location: visualize.php");
?>