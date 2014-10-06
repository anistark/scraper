<?php
include_once('simple_html_dom.php');

function scraping_stackoverflow() {
    // create HTML DOM
    $url='http://stackoverflow.com/questions/tagged/';
    $tag=$_POST['value'];
    $html = file_get_html($url.$tag);

    // get questions block
    foreach($html->find('div[class^=question-summary]') as $questions) {
        // get question url
        $item['question'] = trim($questions->find('a.question-hyperlink', 0)->plaintext);
        // get status
        $item['status'] = trim($questions->find('div.status', 0)->plaintext);

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
    echo '<li>'.$v['question'].'<strong> Status: </strong>'.$v['status'].'</li>';
    echo '</ul>';
}
?>
