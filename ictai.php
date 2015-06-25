<?

/**
*
*
* A html based visualiser for sunny performance on feature selected dataset
*
*
*/

$fss = array("original","ranker1","ranker2","ranker3","ranker4","ranker5","ranker6","ranker7","ranker8","ranker16","ranker32","ranker64");
$types = array("res_sym");
$instFilePath = "inst.txt";

//collecting scenarios 
$scenarios = array();
$handle = fopen($instFilePath, "r");
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $scenarios[] = trim($line);
  }
  fclose($handle);
} else {

    // error opening the file.
} 

$htmResults = htmlFromRankerType($scenarios,$fss,"res_sym");
$htmlPar10_sym = $htmResults[0];
$htmlFsi_sym = $htmResults[1];

$htmResults = htmlFromRankerType($scenarios,$fss,"res_gainratio");
$htmlPar10_ratio = $htmResults[0];
$htmlFsi_ratio = $htmResults[1];

$htmResults = htmlFromRankerType($scenarios,$fss,"res_infogain");
$htmlPar10_infg = $htmResults[0];
$htmlFsi_infg = $htmResults[1];

$htmResults = htmlFromRankerType($scenarios,$fss,"res_relief");
$htmlPar10_relief = $htmResults[0];
$htmlFsi_relief = $htmResults[1];

function htmlFromRankerType($scenarios,$fss,$type){

  $scenario = $scenarios[0];
//$content = $scenario;

  foreach ($scenarios as $scenario) {

    foreach ($fss as $fs) {
      $results = getFsiPar10ForScenarioAndCase($fs,$scenario,$type);
       // echo "$scenario $fs ".$results[0]." <br/>";
      $dic_par10[$scenario][$fs] = $results[0];
      $dic_fsi[$scenario][$fs] = $results[1];
    }

  }


//par10
  foreach ($scenarios as $scenario) {
    $value_par10 = $dic_par10[$scenario]["original"];
    $value_fsi = $dic_fsi[$scenario]["original"];

    foreach ($fss as $fs) {
      if ($fs != "original") {
            //echo "dic_par10[$tmpScenario] [$fs] fsi".$dic_fsi[$tmpScenario][$fs]." par10".$dic_par10[$tmpScenario][$fs]." <br/>";
        if ($dic_par10[$scenario][$fs] == "-") {
                 //echo "- <br/>";
          $diff_par10[$scenario][$fs] =  "-";
          $diff_fsi[$scenario][$fs] =  "-";
        }else{
                // echo "num <br/>";
          $diff_par10[$scenario][$fs] =  round($dic_par10[$scenario][$fs] - $value_par10,5);
          $diff_fsi[$scenario][$fs] = round($dic_fsi[$scenario][$fs]  - $value_fsi,5);
        }
      }

    }
  }


//html

//par10

//table heading
  $htmlContentpt = "<tr><td><label>Scenario</label></td>";
  foreach ($fss as $key => $fs) {
    $htmlContentpt .= "<td><label>$fs</label></td>";
  }
  $htmlContentpt .= "</tr>";

//table content
  foreach ($scenarios as  $scenario) {
    $htmlContentpt .= "<tr>";
    $htmlContentpt .= "<td><label>$scenario</label></td>";
    $value_par10 = $dic_par10[$scenario]["original"];
    $htmlContentpt .= "<td>".round($value_par10,5)."</td>";
    foreach ($fss as $keyargs => $fs) {
      if ($fs != "original") {
        $tmpFsPar10 = $diff_par10[$scenario][$fs];
        $htmlContentpt .= ($tmpFsPar10 >= 0)? "<td class='red'>".$tmpFsPar10."</td>" : "<td class='green'>".$tmpFsPar10."</td>";
      }
    }
    $htmlContentpt .= "</tr>";
  }


//fsi

  $htmlContentfsi = "<tr><td><label>Scenario</label></td>";
  foreach ($fss as $key => $fs) {
    $htmlContentfsi .= "<td><label>$fs</label></td>";
  }
  $htmlContentfsi .= "</tr>";

//table content
  foreach ($scenarios as $scenario) {
    $htmlContentfsi .= "<tr>";
    $htmlContentfsi .= "<td><label>$scenario</label></td>";
    $value_fsi_original = $dic_fsi[$scenario]["original"];
    $htmlContentfsi .= "<td>".round($value_fsi_original,5)."</td>";
    foreach ($fss as $keyargs => $fs) {
      if ($fs != "original") {
        $tmpFsFsi = $diff_fsi[$scenario][$fs];
        $htmlContentfsi .= ($tmpFsFsi >= 0)? "<td class='green'>".$tmpFsFsi."</td>" : "<td class='red'>".$tmpFsFsi."</td>";
      }
    }
    $htmlContentfsi .= "</tr>";
  }

  return array($htmlContentpt,$htmlContentfsi);

}



function getFsiPar10ForScenarioAndCase($case,$scenario,$type){
    // load original

  if ($case == "original") {
    $originalpath = "data/original/".$scenario.".txt";
  }else
  $originalpath = "data/$type/$case/".$scenario.".txt";
  $par10 = array();
  $fsi = array();

  if(!file_exists($originalpath)){
    return array("-","-");
  }
    //open file
  $handle = fopen($originalpath, "r");
  if ($handle) {
    while (($line = fgets($handle)) !== false) {
            // process the line read.
      $pos = strrpos($line, "SUNNY PAR10:");
            if ($pos !== false) { // note: three equal signs
              $splitted = split("PAR10:", $line);
              $par10 =  round(floatval($splitted[1]),3);
            }

            $pos = strrpos($line, "SUNNY FSI:");
            if ($pos !== false) { // note: three equal signs
              $splitted = split("FSI:", $line);
              $fsi = round(floatval($splitted[1]),3);
            }
          }

          fclose($handle);
        }
        return array($par10,$fsi);
      }


      ?>


      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html>

      <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta charset="utf-8"/>
        <link type='text/css' href='css/bootstrap.css' rel='stylesheet' />
        <script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>
        <script type='text/javascript' src='lib/bootstrap.min.js'></script>

        <style>
        .content{
          margin: auto;
          width: 70%;
        }
        .green{
         color: green;
       }
       .red{
         color: red;
       }

       </style>
     </head>

     <body>
      <div class='content'>

        <div class="table-responsive">
          <h2>Par10 symmmetric uncertainty</h2>
          <table class="table">
           <?php echo $htmlPar10_sym;?>
         </table>

         <h2>FSI symmmetric uncertainty</h2>
         <table class="table">
           <?php echo $htmlFsi_sym;?>
         </table>
       </div>

       <div class="table-responsive">
        <h2>Par10 gain ratio</h2>
        <table class="table">
         <?php echo $htmlPar10_ratio;?>
       </table>

       <h2>FSI gain ratio</h2>
       <table class="table">
         <?php echo $htmlFsi_ratio;?>
       </table>
     </div>

     <div class="table-responsive">
      <h2>Par10 info gain</h2>
      <table class="table">
       <?php echo $htmlPar10_infg;?>
     </table>

     <h2>FSI info gain</h2>
     <table class="table">
       <?php echo $htmlFsi_infg;?>
     </table>
   </div>

   <div class="table-responsive">
      <h2>Par10 info relief</h2>
      <table class="table">
       <?php echo $htmlPar10_relief;?>
     </table>

     <h2>FSI info relief</h2>
     <table class="table">
       <?php echo $htmlFsi_relief;?>
     </table>
   </div>


 </body>



 <script>
 </script>


 </html>