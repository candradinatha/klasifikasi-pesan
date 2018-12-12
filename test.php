<?php

    require_once('NaiveBayesClassifier.php');
    require_once('PorterStemmer.php');

    $classifier = new NaiveBayesClassifier();

    $keywordsArray = array();

    $tokenize = $classifier -> tokenize("Nah I do not think he goes to usf, he lives around here though");

    echo json_encode($tokenize);

    foreach ($tokenize as $word) {
        $stemmed = PorterStemmer::Stem($word);
        array_push($keywordsArray, $stemmed);
    }

    echo json_encode($keywordsArray);

    
?>