<?php

require_once 'db_connection.php';

$sql = "SELECT *, otp_failure_count, biometric_failure_count FROM `online_voting_requests`";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Voter Status Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Voter Statuses (Admin View)</h2>
        <table>
            <thead>
                <tr>
                    <th>Voter ID</th>
                    <th>Election ID</th>
                    <th>Online Status</th>
                    <th>OTP Status</th>
                    <th>Biometric Status</th>
                    <th>Voting Status</th>
                </tr>
            </thead>
            <tbody id="voter-table">
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr id="voter_<?php echo $row['id']; ?>">
                    <td><?php echo $row['voter_id']; ?></td>
                    <td><?php echo $row['election_id']; ?></td>
                    <td>
                        <button class="status-btn" id="online_<?php echo $row['id']; ?>"><?php echo $row['online']; ?></button>
                    </td>
                    <td>
                        <button class="status-btn" id="otp_status_<?php echo $row['id']; ?>">
                            <?php echo $row['otp_status']; ?>
                        </button>
                        <?php if ($row['otp_failure_count'] >= 1) { ?>
                            <button class="status-btn failure-count" id="otp_failure_<?php echo $row['id']; ?>">
                                Failures: <?php echo $row['otp_failure_count']; ?>
                            </button>
                        <?php } ?>
                    </td>
                    <td>
                        <button class="status-btn" id="biometric_status_<?php echo $row['id']; ?>">
                            <?php echo $row['biometric_status']; ?>
                        </button>
                        <?php if ($row['biometric_failure_count'] >= 1) { ?>
                            <button class="status-btn failure-count" id="biometric_failure_<?php echo $row['id']; ?>">
                                Failures: <?php echo $row['biometric_failure_count']; ?>
                            </button>
                        <?php } ?>
                    </td>
                    <td>
                        <button class="status-btn" id="voting_status_<?php echo $row['id']; ?>"><?php echo $row['voting_status']; ?></button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
      
        function refreshVoterStatuses() {
            $.ajax({
                url: 'get_voter_statuses.php', 
                method: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);
                    data.forEach(function(voter) {
                        
                        updateStatusColor(voter.id, 'online', voter.online);
                        updateStatusColor(voter.id, 'otp_status', voter.otp_status);
                        updateStatusColor(voter.id, 'biometric_status', voter.biometric_status);
                        updateStatusColor(voter.id, 'voting_status', voter.voting_status);

                        
                        if (voter.otp_failure_count >= 1) {
                            $('#otp_failure_' + voter.id).text('Failures: ' + voter.otp_failure_count).show();
                            $('#otp_failure_' + voter.id).removeClass('verified').addClass('failed'); 
                        } else {
                            $('#otp_failure_' + voter.id).hide(); 
                        }

                       
                        if (voter.biometric_failure_count >= 1) {
                            $('#biometric_failure_' + voter.id).text('Failures: ' + voter.biometric_failure_count).show();
                            $('#biometric_failure_' + voter.id).removeClass('verified').addClass('failed'); 
                        } else {
                            $('#biometric_failure_' + voter.id).hide(); 
                        }
                    });
                }
            });
        }

        function updateStatusColor(voterId, statusType, statusValue) {
            var statusElement = $("#" + statusType + "_" + voterId);
            statusElement.text(statusValue); 

            
            switch (statusValue) {
                case 'active':
                    statusElement.removeClass('inactive failed verified pending').addClass('active');
                    break;
                case 'pending':
                    statusElement.removeClass('verified failed active').addClass('pending');
                    break;
                case 'verified':
                    statusElement.removeClass('pending failed active').addClass('verified');
                    break;
                case 'failed':
                    statusElement.removeClass('pending verified active').addClass('failed');
                    break;
                default:
                    statusElement.removeClass('pending verified failed active');
            }
        }

        
        setInterval(refreshVoterStatuses, 5000); 

        
        refreshVoterStatuses();
    </script>
</body>
</html>


<style>

body {
    font-family: 'Roboto', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}


.container {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 1100px;
    padding: 30px;
    font-size: 16px;
}


h2 {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    color: #0d3d56;
    margin-bottom: 20px;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}


table th {
    background-color: #0d3d56;
    color: #ffffff;
    padding: 12px 15px;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
    text-transform: uppercase;
}


table td {
    padding: 10px 15px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 14px;
    color: #555;
    transition: background-color 0.3s ease, color 0.3s ease;
}


table tr:hover {
    background-color: #f1f1f1;
}


.status-btn {
    padding: 8px 12px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    text-transform: capitalize;
    font-weight: 500;
}


.status-btn.failed {
    background-color: red;
    color: white;
}

.status-btn.verified {
    background-color: green;
    color: white;
}

.status-btn.pending {
    background-color: yellow;
    color: black;
}

.status-btn.active {
    background-color: blue;
    color: white;
}


.status-btn:hover {
    opacity: 0.9;
}


.status-btn:active {
    transform: scale(0.98);
}


footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #0d3d56;
    color: white;
    text-align: center;
    padding: 10px;
    font-size: 14px;
}

footer a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

footer a:hover {
    text-decoration: underline;
}


@media screen and (max-width: 768px) {
    .container {
        padding: 15px;
    }

    table th, table td {
        font-size: 14px;
        padding: 8px 10px;
    }

    h2 {
        font-size: 20px;
    }
}


</style>