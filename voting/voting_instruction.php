<?php
session_start();

$aadhaar_number=$_SESSION['aadhaar_number1'];


$_SESSION['aadhaar_number']=$aadhaar_number;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting Instructions</title>
    <style>
       
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
}


header {
    background-color: #003366;
    color: white;
    text-align: center;
    padding: 20px;
    width: 100%;
}

header img {
    width: 70px;
    height: auto;
    margin-bottom: 8px;
}

header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}


.container {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 90%;
    height:50%;
    max-width: 750px;
    padding: 25px;
    text-align: center;
    margin-top: 20px;
    flex: 1;
}

h1 {
    color: white;
    font-size: 22px;
    margin-bottom: 15px;
}

.instruction {
    display: none;
    font-size: 16px;
    line-height: 1.6;
    text-align: left;
}
.v{
      width: 70%;
    max-width: 600px;
    height: auto;
    border-radius: 8px;
    margin-bottom: 10px;
}
.instruction img {
    width: 100%;
    max-width: 700px;
    height: auto;
    border-radius: 8px;
    margin-bottom: 10px;
}


.contact-authority {
    font-size: 15px;
    color: #d63031;
    margin-top: 15px;
    font-weight: 500;
    display: none;
}


.btn {
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 17px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s ease-in-out;
    margin-top: 15px;
}

.btn:hover {
    background-color: #0056b3;
}


footer {
    background-color: #003366;
    color: white;
    text-align: center;
    padding: 12px 0;
    width: 100%;
    margin-top: auto;
    font-size: 14px;
}


@media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 20px;
    }

    header h1 {
        font-size: 20px;
    }

    .btn {
        font-size: 16px;
        padding: 10px 18px;
    }
}
.t{
    color:black;
}

    </style>
</head>
<body>

    <header>
        <img src="../logo.png" alt="Election Commission Logo">
        <h1>Election Commission of India</h1>
    </header>

    <div class="container">
        <h1 class="t">Online Voting Instructions</h1>

        <div class="instruction" id="instruction1">
            <p><img src="inst1.png" alt="Instruction 1 Image"><br>I. Select your candidate or option by clicking the "Vote" button next to the candidate.</p>
        </div>

        <div class="instruction" id="instruction2">
            <p><img src="inst2.png" class="v" alt="Instruction 2 Image"> Review your vote and confirm your final selection before submitting.</p>
        </div>

        <div class="instruction" id="instruction3">
            <p><img src="inst3.jpg" alt="Instruction 3 Image"><br>You will receive a receipt confirming your vote after final submission.</p>
            <p class="contact-authority" id="contactMessage">
                If you do not receive a confirmation message after submitting your vote, please contact the authority.
            </p>
            
            <label>
                <input type="radio" id="understandRadio" name="understand" onclick="enableCastVoteButton()"> 
                I hereby understand the instructions for online voting clearly.
            </label>

            <button class="btn" id="castVoteButton" onclick="castVote()" disabled>Cast Vote Now</button>
        </div>

        <button class="btn" id="nextButton" onclick="showNextInstruction()">Next</button>
    </div>

 
    <footer>
        <p>&copy; 2025 Election Commission of India. All Rights Reserved.</p>
    </footer>

    <script>
        let currentInstruction = 1;
        const totalInstructions = 3;

 
        document.getElementById('instruction1').style.display = 'block';

        function showNextInstruction() {
            if (currentInstruction < totalInstructions) {
                
                document.getElementById('instruction' + currentInstruction).style.display = 'none';
                currentInstruction++;

                
                document.getElementById('instruction' + currentInstruction).style.display = 'block';

                
                if (currentInstruction === totalInstructions) {
                    document.getElementById('nextButton').style.display = 'none'; 
                    document.getElementById('castVoteButton').style.display = 'block'; 
                }
            }
        }

        function enableCastVoteButton() {
            
            const castVoteButton = document.getElementById('castVoteButton');
            if (document.getElementById('understandRadio').checked) {
                castVoteButton.disabled = false; 
            } else {
                castVoteButton.disabled = true; 
            }
        }

        function castVote() {
        
            window.location.href = "pre_voting.php";
        }
    </script>

    <style>
      
        #castVoteButton {
            display: none;
            background-color: #181feb;
            height: 50px;
            width: 200px;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            display: block; 
        }

        #castVoteButton:hover {
            background-color: #45a049;
        }

        
        label {
            font-size: 16px;
            display: block;
            margin-top: 15px;
        }

        
        .btn {
            background-color: #181feb;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>

</body>
</html>
