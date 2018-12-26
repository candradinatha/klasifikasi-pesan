<?php
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    require_once('NaiveBayesClassifier.php');
    require_once('PorterStemmer.php');

    $classifier = new NaiveBayesClassifier();
    $spam = Category::$SPAM;
    $ham = Category::$HAM;

    $stemmedArray = array();
    $wordIsSpam = array();
    $wordIsHam = array();

    $tokenize = $classifier -> tokenize($email);
    foreach ($tokenize as $word) {
        $stemmed = PorterStemmer::Stem($word);
        array_push($stemmedArray, $stemmed);
    }

    $wordIsSpam = $classifier -> spamPerWord($tokenize);
    $wordIsHam = $classifier -> hamPerWord($tokenize);

    $pSpam = $classifier -> pspam();
    $pHam = $classifier -> pham();

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
    <link rel="stylesheet" type="text/css" media="screen" href="datatables.min.css" />
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
        .nav{
          margin-top: 50px;
        }
        .scrollable {
            overflow-y: scroll;
        }
        .col-9 .card {
            height: 400px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><b>Testing</b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><b>Training</b></a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="container-fluid top">
                <div class="row justify-content-center mt-5">
                    <div class="col-8">
                        <div class="card p-4 ">
                            <h2 class="text-center">Naive Bayes Classifier dengan Laplace Smoothing</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid bottom">
                <div class="row">
                    <div class="col-3">
                        <div class="card p-3">
                            <form action="main.php" method="POST">
                                <div class="form-group">
                                    <h5 for="emailInput">Input Pesan</h5>
                                    <textarea class="form-control" id="emailInput" rows="8" name="email"></textarea>
                                </div>
                                <input class="btn float-right" type="submit" value="submit"> 
                            </form>
                            <h5 class="mt-4">Pesan: </h5>
                            <div class="form-group">
                                <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($email)){echo "";} else echo $email;?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card p-3">
                            <div>
                                <h5>Hasil Stop Word: </h5>
                                <div class="form-group">
                                    <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($tokenize)){echo "";} 
                                        else echo json_encode($tokenize);?>
                                    </textarea>
                                </div>
                                <h5>Hasil Stemming: </h5>
                                <div class="form-group">
                                    <textarea readonly class="form-control" id="emailInput" rows="10" name="email"><?php if(empty($tokenize)){echo "";} 
                                        else echo json_encode($stemmedArray);?>
                                    </textarea>
                                </div>
        
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3">
                            <div class="row">
                                <div class="col-6 scrollable">
                                    <h5>Probabilitas Spam tiap Kata</h5>
                                    <hr>
                                    <table id="table" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kata</th>
                                                <th scope="col">Probabilitas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(empty($stemmedArray)) {
                                                    echo "";
                                                } else {
                                                    foreach ($stemmedArray as $keys => $values) {
                                                        echo "<tr>
                                                                <td>".$values."</td>
                                                                <td>".$wordIsSpam[$keys]."</td>
                                                            </tr>";
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table> 
                                </div>
                                <div class="col-6 scrollable">
                                    <h5>Probabilitas Ham tiap Kata</h5>
                                    <hr>
                                    <table id="table2" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kata</th>
                                                <th scope="col">Probabilitas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(empty($stemmedArray)) {
                                                    echo "";
                                                } else {
                                                    foreach ($stemmedArray as $key => $value) {
                                                        echo "<tr>
                                                                <td>".$value."</td>
                                                                <td>".$wordIsHam[$key]."</td>
                                                            </tr>";
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table> 
                                </div>
                            </div>
                        </div>                    
                    </div>
                </div>
                <div class="row justify-content-center m-4">
                    <div class="col-9">
                        <div class="card p-4 ">
                            <div class ="row mb-5">
                                <div class="col-6">
                                    <h5>P(spam) = <?php if(empty($pSpam)){echo "...";} else echo $pSpam; ?></h5>
                                    <br>
                                    <h5>P(spam | pesan) = P(spam) * P(kata<sub>1</sub> | spam) * ..... P(kata<sub>n</sub> | spam)</h5>
                                    <br>
                                    <h5>Probabilitas Spam:</h5>
                                    <div class="form-group">
                                        <textarea readonly class="form-control" id="emailInput" rows="2" name="email"><?php if(empty($spamWeight)){echo "";
                                            } else echo $spamWeight;?>
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5>P(ham) = <?php if(empty($pHam)){echo "...";} else echo $pHam; ?></h5>
                                    <br>
                                    <h5>P(ham | pesan) = P(ham) * P(kata<sub>1</sub> | ham) * ..... P(kata<sub>n</sub> | ham)</h5>
                                    <br>
                                    <h5>Probabilitas Ham:</h5>
                                    <div class="form-group">
                                        <textarea readonly class="form-control" id="emailInput" rows="2" name="email"><?php if(empty($hamWeight)){echo "";
                                            } else echo $hamWeight;?>
                                        </textarea>
                                    </div> 
                                </div>                
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <h5 class="text-center">Hasil Klasifikasi:</h5>
                                    <h1 class="text-center"><?php if(empty($category)){echo "";
                                            } else echo $category;?>
                                    </h1>     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="container-fluid top">
                <div class="row justify-content-center mt-5">
                    <div class="col-8">
                        <div class="card p-4 ">
                            <h2 class="text-center">Naive Bayes Classifier dengan Laplace Smoothing</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid bottom">
                <div class="row justify-content-center train">
                    <div class="col-5">
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
                    <div class="col-5">
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="datatables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "bPaginate": false
            });
        });

        $(document).ready(function() {
            $('#table2').DataTable({
                "bPaginate": false
            });
        });
    </script>
</body>
</html>


