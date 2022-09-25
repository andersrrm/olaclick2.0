<?php
    ######################
    # Postulante: Anders RM
    # Fecha: 20/09/2022
    # Telefono: +51970502429
    # Corre: PHP
    ######################
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('memory_limit', '-1');
    error_reporting(E_ALL);

	header('Content-type: application/json;');
	$url = './100000_movies.json'; // path to your JSON file
	$data = file_get_contents($url); // put the contents of the file into a variable
	$movies = json_decode($data, true);

    $starttime = microtime(true);
    
	$newArray = [
        'category1'  => ['name' => 'category1'],
        'category2'  => ['name' => 'category2'],
        'category3'  => ['name' => 'category3'],
        'category4'  => ['name' => 'category4'],
        'category5'  => ['name' => 'category5'],
        'category6'  => ['name' => 'category6']
    ];
    foreach ($movies as $movie){
        foreach ($movie['genre'] as $k => $genre){
            $newArray[$genre]['total_movies'] = $newArray[$genre]['total_movies']+1;
            $newArray[$genre]['total_minutes'] += $movie['runtime'];
            $newArray[$genre]['movies'][] = $movie;
        }
    }
    foreach ($newArray as &$cat){
        $cat['average_minutes'] = $cat['total_minutes']/$cat['total_movies'];
    }
    
    $endtime = microtime(true);
    echo $endtime - $starttime;
    //print_r($newArray);

?>

