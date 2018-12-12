<?php
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    require_once('NaiveBayesClassifier.php');
    require_once('PorterStemmer.php');

    $classifier = new NaiveBayesClassifier();
    $spam = Category::$SPAM;
    $ham = Category::$HAM;

    $stemmedArray = array();

    $tokenize = $classifier -> tokenize($email);
    foreach ($tokenize as $word) {
        $stemmed = PorterStemmer::Stem($word);
        array_push($stemmedArray, $stemmed);
    }
    $spamWeight = $classifier -> spam($tokenize);
    $hamWeight = $classifier -> ham($tokenize);
    $category = $classifier -> decide($tokenize);

}

if (isset($_POST['hamTrainer'])) {
    $hamTrainer = $_POST['hamTrainer'];

    require_once('NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $ham = Category::$HAM;

    $classifier -> train($hamTrainer, $ham);
}

if (isset($_POST['spamTrainer'])) {
    $spamTrainer = $_POST['spamTrainer'];

    require_once('NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $spam = Category::$SPAM;

    $classifier -> train($spamTrainer, $spam);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Naive Bayes Classifier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="app.css" />
    <style>
        .card {
            box-shadow: 0 18px 35px rgba(50,50,93,.1), 0 8px 15px rgba(0,0,0,.07);
            border-radius:20px;
            height: 650px;
        }
        .col-8 .card {
            height:200px;
        }
        .top{
            position: relative;
        }
        .bottom{
            position: absolute;
            margin-top:-80px;
        }
        .top .card {
            background-color: rgb(0,176,240);
        }
        .top h2 {
            color: white;
        }
        .btn{
            color: white;
            background-color: rgb(0,188,212);
        }
        .train .card {
            height: 400px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid top">
        <div class="row justify-content-center mt-5">
            <div class="col-8">
                <div class="card p-4 ">
                    <h2 class="text-center">Naive Bayes Classifier</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid bottom">
        <div class="row">
            <div class="col-4">
                <div class="card p-3">
                    <form action="main.php" method="POST">
                        <div class="form-group">
                            <h4 for="emailInput">Input Pesan</h4>
                            <textarea class="form-control" id="emailInput" rows="8" name="email"></textarea>
                        </div>
                        <input class="btn float-right" type="submit" value="submit"> 
                    </form>
                    <h4 class="mt-4">Pesan: </h4>
                    <div class="form-group">
                        <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($email)){echo "";} else echo $email;?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3">
                    <div>
                        <h4>Hasil Stop Word: </h4>
                        <div class="form-group">
                            <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($tokenize)){echo "";} 
                                else echo json_encode($tokenize);?>
                            </textarea>
                        </div>
                        <h4>Hasil Stemming: </h4>
                        <div class="form-group">
                            <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($tokenize)){echo "";} 
                                else echo json_encode($stemmedArray);?>
                            </textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3">
                    <div>
                        <h4>Probabilitas Spam:</h4>
                        <div class="form-group">
                            <textarea readonly class="form-control" id="emailInput" rows="2" name="email"><?php if(empty($spamWeight)){echo "";
                                } else echo $spamWeight;?>
                            </textarea>
                        </div>
                        <h4>Probabilitas Ham:</h4>
                        <div class="form-group">
                            <textarea readonly class="form-control" id="emailInput" rows="2" name="email"><?php if(empty($hamWeight)){echo "";
                                } else echo $hamWeight;?>
                            </textarea>
                        </div>                    
                        <h4>Hasil Klasifikasi:</h4>
                        <div class="form-group">
                            <textarea readonly class="form-control" id="emailInput" rows="2" name="email"><?php if(empty($category)){echo "";
                                } else echo $category;?>
                            </textarea>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-5 mb-5 train">
            <div class="col-4">
                <div class="card p-3">
                    <form action="main.php" method="POST">
                        <div class="form-group">
                            <h5 for="emailInput">Input Ham Training Data</h5>
                            <textarea class="form-control" id="emailInput" rows="10" name="hamTrainer"></textarea>
                        </div>
                        <input class="btn float-right" type="submit" value="submit"> 
                    </form>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3">
                    <form action="main.php" method="POST">
                        <div class="form-group">
                            <h5 for="emailInput">Input Spam Training Data</h5>
                            <textarea class="form-control" id="emailInput" rows="10" name="spamTrainer"></textarea>
                        </div>
                        <input class="btn float-right" type="submit" value="submit"> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


