<?php


include "conect.php";
$check=false;
if(isset($_POST["check"])){
    $check=true;
    $sentence=explode(" ", strtolower($_POST["sentence"]));
    $table=Array();

    //Pengisian awal array
    for($i=0; $i<count($sentence); $i++){
        for($j=0; $j<count($sentence); $j++){
            if($j==$i){
                $table[$i][$j]=cekLesikon($sentence[$i],$conn);
            }else if($j>=$i){
               $table[$i][$j]=null;
            }
            else{
                $table[$i][$j]='0';
            }
        }
    }
    

   //Proses pengisian tabel filling
    $iter=1;
    $arr1=array();
    $arr2=Array();
    $l=0;
    $r=0;
    for($i=count($sentence)-1; $i>=0; $i--){
        for($j=0; $j<$i; $j++){
            $cek='';
            $p= $j+$iter;
            $set=1;
            for($y=0; $y<$iter; $y++){
                $l= $j+$set-1;
                $r= $j+$set;

                //melakukan komninasi
                $list=explode(' ',$table[$j][$l]);
                $list2=explode(' ',$table[$r][$p]);
                $arr1=getCombinations($list, $list2);
                $arr2=array_merge($arr2,$arr1);
                $arr1=[]; $list=[]; $list2=[];

                $set++;
                
            }
           

            $datahead=Array();
            $arr2=array_unique($arr2);
            //mengcek hasil union di db
            foreach($arr2 as $body){
                $query=mysqli_query($conn,"select * from rule where body='$body'");
                if(mysqli_num_rows($query)>0){
                    while($data=mysqli_fetch_array($query)){
                        array_push($datahead,$data['head']);
                    }
                }
            }

            $table[$j][$j+$iter]= implode(' ',array_unique($datahead));
            $arr2=[]; 
            
        }

    $iter++;
    }
    $validSentece='none';
    if( in_array('K',explode(' ',$table[0][count($sentence)-1])) ){
        $validSentece='VALID';
    }else{
        $validSentece='TIDAK VALID';
    }

}

function cekLesikon($key,$conn){
    $query=mysqli_query($conn,"select * from rule where body='$key'");
    if(mysqli_num_rows($query)>0){
        while($data=mysqli_fetch_array($query)){
            $result[]=$data['head'];
        }
        return implode(' ',$result);
    }else{
        return "0";
    }
}

// fungsi untuk menghasilkan kombinasi
function getCombinations(...$arrays) {
	$result = [[]];
	foreach ($arrays as $property => $property_values) {
		$temp = [];
		foreach ($result as $result_item) {
			foreach ($property_values as $property_value) {
				$temp[] = array_merge($result_item, [$property => $property_value]);
			}
		}
		$result = $temp;
    }
    for($w=0; $w<count($result); $w++){ $result[$w]=implode(' ',$result[$w]); }
	return $result;
}

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#box").hide(1000);
            $("#tombol_hide").hide(1000);
            $("#tombol_hide").click(function() {
            $("#box").hide(1000);
            $("#tombol_show").show(1000);
            $("#tombol_hide").hide(1000);
            })
        
            $("#tombol_show").click(function() {
            $("#box").show(1000);
            $("#tombol_hide").show(1000);
            $("#tombol_show").hide(1000);
            })
        
        });
   </script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!-- CSS style.css-->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Title -->
    <title>CFG - Creative Technology</title>
    

  </head>
  
  <body style="background-color:#2c4666">
    <!-- awal navbar -->
    <?php include "navbar.php";?>
    <!-- akhir navbar -->

    <div class="container bg-light" style="margin-top:35px;width:1080px;padding:50px">
      <div class="warna">
        <h4 class="alert alert-primary text-center">Pengaplikasian CFG dalam penentuan validasi kalimat bahasa Bali</h4>
        <br><br>
        <a href="akurasi.php" class="btn btn-outline-info">Uji Akurasi Sistem</a>
        <br><br>
        <h5>Masukan kalimat</h5>
        <ul class="navbar-nav">
        <form class="form-inline" action="" method="POST">
            <input class="form-control mr-sm-2" name="sentence"type="text" placeholder="Ketik kalimat bahasa bali disini" style="width:850px;" required>
            <button class="btn btn-outline-primary" type="submit" name="check">Check</button>
        </form>
        </ul><br>
        
        <?php if($check==true): ?>

            <!-- HASIL -->
            <h6> <strong>Kalimat Input :</strong></h6>
            <p><i><?=$_POST['sentence'] ?></i></p>
            <br>
            <h6> <strong>Hasil Pengecekan :</strong></h6>
            <?php if($validSentece=='VALID'){?>
                 <p class="text-success"><?=$validSentece?></p>
            <?php } else {?>
                <p class="text-danger"><?=$validSentece?></p>
            <?php } ?>
            <!-- LIHAT TABEL FILLING -->
            <br>
            <h6> <strong>Tabel Filling :</strong></h6>
            <button id="tombol_hide" class="btn btn-warning">Tutup Tabel</button>
            <button id="tombol_show" class="btn btn-primary">Lihat Tabel</button>
            <br><br>


            <div class="row justify-content-center" id="box">
                <div class="col-md-10">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <?php for($j=-1; $j<count($sentence); $j++){ ?>
                                  <?php if($j>=0){?>
                                     <th class="text-center bg-info text-white"> <?=$j?> </th>
                                  <?php }else{ ?>
                                      <th class="bg-info"> </th>
                                  <?php } ?>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  for($i=0; $i<count($sentence); $i++){ ?>
                                <tr>
                                    <?php for($j=-1; $j<count($sentence); $j++){ ?>
                                        <?php if($j!=-1){?>
                                            <td class="text-center"> <?= $table[$i][$j] ?> </td>
                                        <?php }else{ ?>
                                            <th class="text-center bg-info text-white"><?=$i?></th>
                                        <?php } ?>
                                    <?php } ?>
                                </tr>
                            <?php } ?>                 
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php endif; ?>
        



      </div>
    </div>
    

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <!-- Font awesome JQuery script -->
    <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" data-auto-replace-svg="nest"></script>

  </body>
</html>