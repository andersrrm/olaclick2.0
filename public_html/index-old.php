<?php
    ######################
    # Postulante: Anders RM
    # Fecha: 20/09/2022
    # Telefono: +51970502429
    # Corre: PHP
    ######################

    $var=0;
    do {

        $movie = $movies[$var];
        $var2=0;
        do {
            $value=$movie['genre'][$var2];
            $newArray[$value]['name']=$value;
            $newArray[$value]['total_movies']=$newArray[$value]['total_movies']+1;

            $movieArray = [];
            foreach ($movie as $key2=>$value2){
                $movieArray[$key2] = $value2;
            }
            $newArray[$value]['total_minutes'] += $movieArray['runtime'];
            $newArray[$value]['average_minutes'] = $newArray[$value]['total_minutes']/$newArray[$value]['total_movies'];

            unset($movieArray['id']);
            unset($movieArray['rated']);
            unset($movieArray['released']);
            unset($movieArray['genre']);

            $newArray[$value]['movies'][] = $movieArray;

        } while (++$var2 < count($movie['genre']));

    $start = microtime(true); //0.0001
    
	header('Content-type: application/json;');
	$url = './movies.json'; // path to your JSON file
	$data = file_get_contents($url); // put the contents of the file into a variable
    $movies = json_decode($data, true);

    $newArray = [
        'Action' => [
            'name' => 'Action',
        ],
        'Crime'  => [
            'name' => 'Crime',
        ],
        'Drama'  => [
            'name' => 'Drama',
        ],
        'Comedy'  => [
            'name' => 'Comedy',
        ]
    ];

    

    $lastPos = 0;
    $positions = array();
    $minutes = array();
    $needle = 'Drama';
    $movies = array();

    while (($lastPos = strpos($data, $needle, $lastPos))!== false) {
        $positions[] = $lastPos;
        $string = substr($data,$lastPos-60,500);
        $var = string_between_two_string($string, 'runtime','genre');
        $each = [];
        $each['director'] = string_between_two_string($string, 'director": "','"');
        $each['writer'] = string_between_two_string($string, 'writer": "','"');
        $each['actors'] = string_between_two_string($string, 'actors": "','"');
        $each['plot'] = string_between_two_string($string, 'plot": "','"');
        $each['lenguage'] = string_between_two_string($string, 'lenguage": "','"');
        $movies[] = $each;
        $minutes[] = (int) filter_var($var, FILTER_SANITIZE_NUMBER_INT);
        
        $lastPos = $lastPos + strlen($needle);
    }
    
    $newArray['Comedy']['total_movies'] = count($positions);
    $newArray['Comedy']['total_minutes'] = array_sum($minutes);
    $newArray['Comedy']['average_minutes'] = array_sum($minutes)/count($minutes);
    $newArray['Comedy']['movies'] = $movies;

    $array = explode('},',$data);
        
	echo(microtime(true) - $start);
    print_r(json_encode($data,JSON_PRETTY_PRINT));

    function string_between_two_string($str, $starting_word, $ending_word)
    {
        $subtring_start = strpos($str, $starting_word);
        $subtring_start += strlen($starting_word); 
        $size = strpos($str, $ending_word, $subtring_start) - $subtring_start; 
        return substr($str, $subtring_start, $size); 
    }
?>
