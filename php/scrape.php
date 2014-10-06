<?php
include_once('simple_html_dom.php');

$count = 0;

function scraping_stackoverflow() {
    // create HTML DOM
    $url='http://stackoverflow.com/questions/tagged/'.$_POST['value'];
    $html = file_get_html($url);

    // get questions block
    foreach($html->find('div[class^=question-summary]') as $questions) {
        // get question url
        $item['question'] = trim($questions->find('a.question-hyperlink', 0)->plaintext);
        // get question vote
        $item['vote'] = trim($questions->find('span.vote-count-post', 0)->plaintext);
        // get status
        $item['status'] = trim($questions->find('div.status', 0)->plaintext);
        // get views
        $item['views'] = trim($questions->find('div.views', 0)->plaintext);
        
        $ret[] = $item;
    }
    
    // clean up memory
    $html->clear();
    unset($html);

    return $ret;
}

// -----------------------------------------------------------------------------
// test it!
$ret = scraping_stackoverflow();

foreach($ret as $v) {
    echo $v['questions'].'<br>';
    echo '<ul>';
    echo '<li>'.$v['question'].'<strong> Votes: </strong>'.$v['vote'].'<strong> Status: </strong>'.$v['status'].'<strong> Viewed: </strong>'.$v['views'].'</li>';
    echo '</ul>';
    if ($v['status']=='0answers')
        $count = $count+1;
}

// json part
$file = file_get_contents('data.json');
$data = json_decode($file);
unset($file);//prevent memory leaks for large json.
//insert data here
$data[] = array('data'=>'scraper data');
//save the file
file_put_contents('data.json',json_encode($ret));
unset($data);//release memory

$file = file_get_contents('data.json');
$json_array  = json_decode($file, true);
unset($file);
$elementCount  = count($json_array);

// Display
$answered_count = $elementCount - $count;
echo 'Total questions: '.$elementCount.'<br />';
echo 'Total Answered questions: '.$answered_count;

?>
