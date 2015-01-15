<?php
$num = NULL;
if (isset($_GET['num'])) {
    $num=htmlspecialchars($_GET['num']);
}
$images = array();
require __DIR__ . '/goutte.phar';
$client = new Goutte\Client;
$url = "http://matome.naver.jp/odai/".$num;
$crawler = $client->request('GET', $url);
while (1) {
    $crawler->filter('img.MTMItemThumb')
        ->each(function ($nodes, $i) use (&$images){
            if(empty($nodes)){break;}
            foreach($nodes as $node)
                (new Goutte\Client)->request('GET', $node->parentNode->getAttribute("href"))
                    ->filter('div.LyMain p.mdMTMEnd01Img01 img.class')
                    ->each(function ($nodes, $i) use (&$images) {
                        foreach($nodes as $node){
                            $images[] = $node->ownerDocument->saveHTML($node)."\n";
                        }
                    });
        });
    try {
        $nextPage = $crawler
            ->filter('div.MdPagination03 strong')->nextAll()->text();
        sleep(1);
        $crawler = $client->request('GET', $url."?page=$nextPage");
    } catch (Exception $e) {
        $images[] =  "all gone.";
        break;
    }
    
}


?>
<html>
<body>

<form name="input" action="." method="get">

<input type="text" name="num" value="<?php echo empty($num) ? '2131355192254813101' : $num ?>" size="25">
<br>
<input type="submit" value="submit">
</form>

<?php echo implode("\n",$images) ?>

</body>
</html>