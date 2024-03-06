<html>
<head>
  <title>Ballot Paper</title>
 </head>
 <body>
 <?php 
 session_start();
 //echo '<p>Establishing a connection to an Oracle database.</p>'; 

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
else {
    //echo 'Successfully connect<BR>';
    
    //query
    $checkElectoralRole = "SELECT *
        FROM regvoter
        WHERE lower(firstname || ' '|| lastname) LIKE :voter_fullname
        AND lower(streetno || ' '|| streetname) LIKE :voter_line1
            AND lower(state) LIKE :voter_state
            AND postcode LIKE :voter_pc";


    // Assign form inputs to PHP variables
    $fullname = $_POST["voter_name"]; 
    $addressline1 = $_POST["line1"];
    $state = $_POST["state"];
    $postcode = $_POST["postcode"];

    //Trim any whitespace at 2 ends
    $fullname = trim($fullname);
    $addressline1 = trim($addressline1);
    $state = trim($state);
    $postcode = trim($postcode);

    //convert them into lowercase
    $fullname = strtolower($fullname);
    $addressline1 = strtolower($addressline1);
    $state = strtolower($state);
  
    $runCheckElectoralRole = oci_parse($conn,$checkElectoralRole);
    //binding php variables to Oracle bind variables
    oci_bind_by_name($runCheckElectoralRole, ":voter_fullname", $fullname);
    oci_bind_by_name($runCheckElectoralRole, ":voter_line1", $addressline1);
    oci_bind_by_name($runCheckElectoralRole, ":voter_state", $state);
    oci_bind_by_name($runCheckElectoralRole, ":voter_pc", $postcode);

    
    oci_execute($runCheckElectoralRole);

    
    

    

    $numrows = oci_fetch_all($runCheckElectoralRole, $res); 
    //echo "$numrows rows fetched<br>\n";
    if ($numrows == 1){
        //echo 'Voter is registered in this Electorate!';

        //Retrieve voterID and Electorate
        $getVoterInfo = "SELECT voterid, electorate
          FROM regvoter r JOIN postcodelectorate p
          ON r.postcode = p.postcode
          WHERE lower(firstname || ' '|| lastname) LIKE :voter_fullname
          AND lower(streetno || ' '|| streetname) LIKE :voter_line1
            AND lower(state) LIKE :voter_state
            AND p.postcode LIKE :voter_pc";
        
        $runGetVoterInfo = oci_parse($conn,$getVoterInfo);
        //binding php variables to Oracle bind variables
        oci_bind_by_name($runGetVoterInfo, ":voter_fullname", $fullname);
        oci_bind_by_name($runGetVoterInfo, ":voter_line1", $addressline1);
        oci_bind_by_name($runGetVoterInfo, ":voter_state", $state);
        oci_bind_by_name($runGetVoterInfo, ":voter_pc", $postcode);

        oci_execute($runGetVoterInfo);
        //Assign voterID and Electorate to php variables
        while (($row = oci_fetch_assoc($runGetVoterInfo)) != false) {
          $voterid = $row['VOTERID'];
          $electorate = $row['ELECTORATE'];
      };
        //echo '<br>VoterID:'.$voterid . "<br>" .'Electorate:'. $electorate . "<br>\n";

        $previouslyVoted = "CREATE OR REPLACE FUNCTION previouslyVoted(inputelectionid IN electionkey.electionid%type,
                            inputelectorate IN electorate.electorate%type,
                            inputvoterid IN regvoter.voterid%type)
        RETURN VARCHAR
        AS 
          output VARCHAR(10) := 'FALSE';
        BEGIN
          FOR rec IN (SELECT r.voterid
          FROM regvoter r LEFT JOIN issuerec i ON r.voterid = i.voterid
          WHERE i.electionid = inputelectionid AND lower(i.electorate) = lower(inputelectorate) AND r.voterid = inputvoterid)
          LOOP
          output := 'TRUE';
          EXIT;
          END LOOP;
          RETURN output;
        EXCEPTION
          WHEN NO_DATA_FOUND THEN
          DBMS_OUTPUT.put_line('Invalid input');
        END previouslyVoted;";

        $runPreviouslyVoted = oci_parse($conn,$previouslyVoted);
        oci_execute($runPreviouslyVoted);

        //Call the Stored Function
        $previouslyVoted2022 = "SELECT previouslyVoted(:electionid,:electorate,:voterid) FROM dual";

        $runPreviouslyVoted2022 = oci_parse($conn,$previouslyVoted2022);

        $electorate = strtolower($electorate);

        $electionid = 20220521;
        oci_bind_by_name($runPreviouslyVoted2022, ":electionid", $electionid);
        oci_bind_by_name($runPreviouslyVoted2022, ":electorate", $electorate);
        oci_bind_by_name($runPreviouslyVoted2022, ":voterid", $voterid);
        

        oci_execute($runPreviouslyVoted2022);

        while (($row = oci_fetch_assoc($runPreviouslyVoted2022)) != false) {
          $voteYetOutput = $row['PREVIOUSLYVOTED(:ELECTIONID,:ELECTORATE,:VOTERID)'];
      };

        //echo $voteYetOutput;

        if (strtolower($voteYetOutput) == 'false' ) {
          //echo "<br> YOU CAN VOTE YAYYYY <br>";

          //convert state name
          if ($state == 'vic') {$stateFull = 'Victoria';}
          elseif ($state == 'qld') {$stateFull = 'Queensland';}
          elseif ($state == 'tas') {$stateFull = 'Tasmania';}
          elseif ($state == 'nsw') {$stateFull = 'New South Wales';}
          elseif ($state == 'wa') {$stateFull = 'Western Australia';}
          elseif ($state == 'sa') {$stateFull = 'South Australia';}
          elseif ($state == 'nt') {$stateFull = 'Northern Territory';}
          elseif ($state == 'act') {$stateFull = 'Australian Capital Territory';};

          $electorateUC = ucfirst($electorate);
          
          //echo $state . "<br>";
          //echo $electorate . '<br>';

          //Logo at the top of the page
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

          //Get number of candidates in that electorate
          $getCandidateCount = "SELECT *
          FROM candidate c JOIN party p
          ON c.partycode = p.partycode
          WHERE lower(electorate) = :electorate";

          $runGetCandidateCount = oci_parse($conn,$getCandidateCount);
          oci_bind_by_name($runGetCandidateCount, ":electorate", $electorate);
          oci_execute($runGetCandidateCount);
          $canCount = oci_fetch_all($runGetCandidateCount, $res); 
           

          echo "<html>
          <p style ='font-size:35px;font-family:verdana; padding-left: 30px'>$stateFull<br>Electoral Division of $electorateUC</p>
          <p style='padding-left: 30px;font-family:verdana'>Number the boxes from 1 to $canCount in order of your choice.</p>
          </html>";

          $_SESSION['candCount'] = $canCount;

          //get candidate info


          // $getCandidate = "SELECT *
          // FROM candidate c JOIN party p
          // ON c.partycode = p.partycode
          // WHERE lower(electorate) = :electorate";

          // $runGetCandidate = oci_parse($conn,$getCandidate);
          // oci_bind_by_name($runGetCandidate, ":electorate", $electorate);
          // oci_execute($runGetCandidate);
        
        $getCandidate = 'SELECT *
          FROM candidate c JOIN party p
          ON c.partycode = p.partycode
          WHERE lower(electorate) = :electorate';

          $runGetCandidate = oci_parse($conn,$getCandidate);
          oci_bind_by_name($runGetCandidate, ':electorate', $electorate);
          oci_execute($runGetCandidate);
          
        
        $canIDlist = array();
        
        

        echo  "
        <html>
        <html>
        <form id='ballotform' name='ballotform'  action='ballotprocessing.php' method='post' style='font-family:verdana; padding-left: 35px;'>
        </html> ";
        $i = 0;
        while (($row = oci_fetch_array($runGetCandidate, OCI_BOTH)) != false) {
          $pref="pref".$i;
          $canid = $row['CANDIDATEID'];
          array_push($canIDlist,$canid);
          $canln = strtoupper($row['LASTNAME']);
          $canfn = ucfirst($row['FIRSTNAME']);
          $party = strtoupper($row['PARTYNAME']);   
          echo "
          <html>
            <style>
            .candidateBox {
              display: flex;
              align-items: center;
            }
            span {
              padding: 10px;
            }
          </style>
          <div class='candidateBox'>
          <input type='text' form='ballotform' name=$pref style='width:30px;height:30px;'> 
          <span>$canln, $canfn<br>$party</span>
          </div>
          </html>";
          $i = $i +1;
          };
        echo "
        <html>
        <input type='submit' form='ballotform' value='VOTE'>
        </form>
        </html>";

      
            
            //var_dump($_SESSION['canList']);
            $_SESSION['electorate'] = $electorate;
            $_SESSION['canList'] = $canIDlist;
            $_SESSION['electionid'] = $electionid;
            $_SESSION['voterid'] = $voterid;

            
        
      }
        else {
          echo "<script>
          alert('WARNING:According to our database, you have already voted in this election even though you claimed that you have not voted. This action is considered as fraud. Please do not do this again.');
          window.location.href='index.html';
          </script>";
        }

    } else {
      echo "<script>
      alert('Your information is not found in the records. Please double check and fill in the information again');
      window.location.href='index.html';
      </script>";
    }

    
}

oci_close($conn);
?>


</html>