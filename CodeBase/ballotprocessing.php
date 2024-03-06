<html>
 <head>
  <title>Processing Ballot Paper</title>
 </head>
 <body>
 <?php  
 session_start();


// establish a database connection to your Oracle database.
$username = '###';
$password = '###';
$servername = '###';
$servicename = '###';
$connection = $servername."/".$servicename;

$conn = oci_connect($username, $password, $connection);
if(!$conn) 
{
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
else 
{
    echo ' 
          <html>
          <style>
          .arrangement {
          display: flex;
          align-items: center;
          justify-content: left}
          </style>
          <div class="arrangement">
          <img src="logonotext.png" alt="Logo of Australia Government" width=150px height=100px style="float:left" />
          <p style="font-size:20px;padding-left: 30px;" ><b>House of Representatives <br> Ballot Paper</b></p>
          </div>
          <br>
          <hr class="solid">
          </html>';
    

    $canNum = $_SESSION['candCount'];
    $electorate = ucfirst($_SESSION['electorate']);
    $canIDlist = $_SESSION['canList'];
    $electionid = 20220521;
    $voterid = $_SESSION['voterid'];

    //Create ballotid
    $maxBallotID = "SELECT max(ballotid) FROM ballot";
    $runMaxBallotID = oci_parse($conn,$maxBallotID);
    oci_execute($runMaxBallotID);

    while (($row = oci_fetch_assoc($runMaxBallotID)) != false) {
        $lastBallotID = $row['MAX(BALLOTID)'];
    };

    $ballotid = $lastBallotID + 1;

    //Update Ballot table
    $updateBallot = "INSERT into BALLOT (BALLOTID,TIMESTAMP,POLLINGSTATION,ELECTIONID,ELECTORATE) values (:ballotid,CURRENT_TIMESTAMP,null,:electionid,:electorate)";
    $runUpdateBallot = oci_parse($conn,$updateBallot);
    //binding php variables to Oracle variables
    oci_bind_by_name($runUpdateBallot, ":ballotid", $ballotid);
    oci_bind_by_name($runUpdateBallot, ":electionid", $electionid);
    oci_bind_by_name($runUpdateBallot, ":electorate", $electorate);
    $UB = oci_execute($runUpdateBallot);
if (!$UB) {} else {

    for($i=0;$i<=$canNum-1;$i++) {
        $canID = $canIDlist[$i];
        $prefNo="pref".$i;
        $pref = $_POST[$prefNo];
        $electorate = ucfirst($electorate);

        //Update BallotPref
        $updateBallotPref = "INSERT into BALLOTPREF (PREF,CANDIDATEID,BALLOTID) values (:pref,:canid,:ballotid)";
        $runUpdateBallotPref = oci_parse($conn,$updateBallotPref);
        oci_bind_by_name($runUpdateBallotPref, ":pref", $pref);
        oci_bind_by_name($runUpdateBallotPref, ":canid", $canID);
        oci_bind_by_name($runUpdateBallotPref, ":ballotid", $ballotid);
        oci_execute($runUpdateBallotPref);
    };

    

    //Update IssueRec Table
    $updateIssueRec = "INSERT into ISSUEREC (TIMESTAMP,POLLINGSTATION,VOTERID,ELECTIONID,ELECTORATE) values (CURRENT_TIMESTAMP,null,:voterid,:electionid,:electorate)";
    $runUpdateIssueRec = oci_parse($conn,$updateIssueRec);
    oci_bind_by_name($runUpdateIssueRec, ":voterid", $voterid);
    oci_bind_by_name($runUpdateIssueRec, ":electionid", $electionid);
    oci_bind_by_name($runUpdateIssueRec, ":electorate", $electorate);
    $UIR = oci_execute($runUpdateIssueRec); 

    if (!$UIR) {} else {
        echo "<script>
      alert('Your vote has been successfully submitted. Thank you for voting!');
      window.location.href='index.html';
      </script>";
    }

}; 

    

}

oci_close($conn);

?> 

 </body>
</html>
