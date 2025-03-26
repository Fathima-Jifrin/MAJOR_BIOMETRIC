<?php
session_start();


if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Please log in to access your profile.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id']; 
require 'config.php';

$stmt = $conn->prepare("SELECT full_name, aadhaar_number, phone_number, date_of_birth, email, registration_date FROM voter_registration WHERE id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$ad = $user['aadhaar_number'];

$stmt1 = $conn->prepare("SELECT * from aadhaar_data WHERE aadhaar_number = ?");
$stmt1->bind_param("s", $ad);
$stmt1->execute();
$result1 = $stmt1->get_result();
$user1 = $result1->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form-A: Voter Application - Election Commission of India</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
    <script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
      }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
    <header>
        <img src="logo.png" alt="Election Commission Logo" class="logo"> 
        <h1>Election Commission of India</h1>
        <h2>Form 6</h2>
        <p>Voter Application Form for Shifting of Name, Replacement of Electoral Roll, or Duplicate ID Card</p>
        <script>
        function confirmSubmission() {
            return confirm("Are you sure you want to submit? The information provided is correct, and no changes are applicable after submission.");
        }
    </script>
    </header>

    <div id="google_translate_element"></div>

    <div class="container">
        <form id="voterForm" action="preview.php" method="POST">
            
            <div class="form-section" id="section-1">
                <h3>1. Applicant's Details</h3>
                <label for="full_name">Full Name (As per Official Records):</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>

                <label for="surname">Surname (if any):</label>
                <input type="text" id="surname" name="surname" required>

                <label for="dob">Date of Birth (DD/MM/YYYY):</label>
                <input type="text" id="dob" name="dob" placeholder="DD/MM/YYYY" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" readonly    >

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <label for="place_of_birth">Place of Birth:</label>
                <input type="text" id="place_of_birth" name="place_of_birth">
                
                <button type="button" class="next-btn">Next</button>
            </div>


            <div class="form-section" id="section-2" style="display:none;">
                <h3>2. Government Document</h3>

                <label for="district">Adhaar Number:</label>
                <input type="text" id="adhaar" name="adhaar" value="<?php echo htmlspecialchars($user['aadhaar_number']); ?>" readonly>

                <button type="button" class="prev-btn">Previous</button>
                <button type="button" class="next-btn">Next</button>
            </div>





            <div class="form-section" id="section-3" style="display:none;">
                <h3>3. Address Details</h3>
                <label for="current_address">Current Residential Address (As per Proof of Residence):</label>
                <textarea id="current_address" name="current_address"value="<?php echo htmlspecialchars($user1['address']); ?>" rows="3" required   readonly><?php echo htmlspecialchars($user1['address']); ?></textarea>

                <label for="permanent_address">Permanent Address (if different):</label>
                <textarea id="permanent_address" name="permanent_address" rows="3"></textarea>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" required>

                <label for="district">District:</label>
                <input type="text" id="district" name="district" required>

                <label for="pincode">Pincode:</label>
                <input type="text" id="pincode" name="pincode" required>

                <button type="button" class="prev-btn">Previous</button>
                <button type="button" class="next-btn">Next</button>
            </div>

       
            <div class="form-section" id="section-4" style="display:none;">
                <h3>4. Constituency Selection</h3>

                <label for="constituency">Select Constituency:</label>
                <select id="constituency" name="constituency" required>
                    <option value="">-- Select Constituency --</option>
                    
                    <optgroup label="Kasaragod">
                        <option value="1- Manjeshwar">1- Manjeshwar</option>
                        <option value="2- Kasaragod">2- Kasaragod</option>
                        <option value="3- Udma">3- Udma</option>
                        <option value="4- Kanhangad">4- Kanhangad</option>
                        <option value="5- Trikaripur">5- Trikaripur</option>
                        <option value="6- Payyannur">6- Payyannur</option>
                        <option value="7- Kalliasseri">7- Kalliasseri</option>
                    </optgroup>
                
                    <optgroup label="Kannur">
                        <option value="8- Taliparamba">8- Taliparamba</option>
                        <option value="9- Irikkur">9- Irikkur</option>
                        <option value="10- Azhikode">10- Azhikode</option>
                        <option value="11- Kannur">11- Kannur</option>
                        <option value="12- Dharmadam">12- Dharmadam</option>
                        <option value="15- Mattannur">15- Mattannur</option>
                        <option value="16- Peravoor">16- Peravoor</option>
                    </optgroup>
                
                    <optgroup label="Vadakara">
                        <option value="13- Thalassery">13- Thalassery</option>
                        <option value="14- Kuthuparamba">14- Kuthuparamba</option>
                        <option value="20- Vadakara">20- Vadakara</option>
                        <option value="21- Kuttiadi">21- Kuttiadi</option>
                        <option value="22- Nadapuram">22- Nadapuram</option>
                        <option value="23- Quilandy">23- Quilandy</option>
                        <option value="24- Perambra">24- Perambra</option>
                    </optgroup>
                
                    <optgroup label="Wayanad">
                        <option value="17- Mananthavady (ST)">17- Mananthavady (ST)</option>
                        <option value="18- Sulthanbathery (ST)">18- Sulthanbathery (ST)</option>
                        <option value="19- Kalpetta">19- Kalpetta</option>
                        <option value="32- Thiruvambadi">32- Thiruvambadi</option>
                        <option value="34- Ernad">34- Ernad</option>
                        <option value="35- Nilambur">35- Nilambur</option>
                        <option value="36- Wandoor (SC)">36- Wandoor (SC)</option>
                    </optgroup>
                
                    <optgroup label="Kozhikode">
                        <option value="25- Balusseri (SC)">25- Balusseri (SC)</option>
                        <option value="26- Elathur">26- Elathur</option>
                        <option value="27- Kozhikode North">27- Kozhikode North</option>
                        <option value="28- Kozhikode South">28- Kozhikode South</option>
                        <option value="29- Beypore">29- Beypore</option>
                        <option value="30- Kunnamangalam">30- Kunnamangalam</option>
                        <option value="31- Koduvally">31- Koduvally</option>
                    </optgroup>
                
                    <optgroup label="Malappuram">
                        <option value="33- Kondotty">33- Kondotty</option>
                        <option value="37- Manjeri">37- Manjeri</option>
                        <option value="38- Perinthalmanna">38- Perinthalmanna</option>
                        <option value="39- Mankada">39- Mankada</option>
                        <option value="40- Malappuram">40- Malappuram</option>
                        <option value="41- Vengara">41- Vengara</option>
                        <option value="42- Vallikkunnu">42- Vallikkunnu</option>
                    </optgroup>
                
                    <optgroup label="Ponnani">
                        <option value="43- Tirurangadi">43- Tirurangadi</option>
                        <option value="44- Tanur">44- Tanur</option>
                        <option value="45- Tirur">45- Tirur</option>
                        <option value="46- Kottakkal">46- Kottakkal</option>
                        <option value="47- Thavanur">47- Thavanur</option>
                        <option value="48- Ponnani">48- Ponnani</option>
                        <option value="49- Thrithala">49- Thrithala</option>
                    </optgroup>
                
                    <optgroup label="Palakkad">
                        <option value="50- Pattambi">50- Pattambi</option>
                        <option value="51- Shoranur">51- Shoranur</option>
                        <option value="52- Ottappalam">52- Ottappalam</option>
                        <option value="53- Kongad (SC)">53- Kongad (SC)</option>
                        <option value="54- Mannarkkad">54- Mannarkkad</option>
                        <option value="55- Malampuzha">55- Malampuzha</option>
                        <option value="56- Palakkad">56- Palakkad</option>
                    </optgroup>
                
                    <optgroup label="Alathur (SC)">
                        <option value="57- Tarur (SC)">57- Tarur (SC)</option>
                        <option value="58- Chittur">58- Chittur</option>
                        <option value="59- Nemmara">59- Nemmara</option>
                        <option value="60- Alathur">60- Alathur</option>
                        <option value="61- Chelakkara (SC)">61- Chelakkara (SC)</option>
                        <option value="62- Kunnamkulam">62- Kunnamkulam</option>
                        <option value="65- Wadakkanchery">65- Wadakkanchery</option>
                    </optgroup>
                
                    <optgroup label="Thrissur">
                        <option value="63- Guruvayoor">63- Guruvayoor</option>
                        <option value="64- Manalur">64- Manalur</option>
                        <option value="66- Ollur">66- Ollur</option>
                        <option value="67- Thrissur">67- Thrissur</option>
                        <option value="68- Nattika (SC)">68- Nattika (SC)</option>
                        <option value="70- Irinjalakuda">70- Irinjalakuda</option>
                        <option value="71- Pudukkad">71- Pudukkad</option>
                    </optgroup>
                
                    <optgroup label="Chalakudy">
                        <option value="69- Kaipamangalam">69- Kaipamangalam</option>
                        <option value="72- Chalakudy">72- Chalakudy</option>
                        <option value="73- Kodungallur">73- Kodungallur</option>
                        <option value="74- Perumbavoor">74- Perumbavoor</option>
                        <option value="75- Angamaly">75- Angamaly</option>
                        <option value="76- Aluva">76- Aluva</option>
                        <option value="84- Kunnathunad (SC)">84- Kunnathunad (SC)</option>
                    </optgroup>
                
                    <optgroup label="Ernakulam">
                        <option value="77- Kalamassery">77- Kalamassery</option>
                        <option value="78- Paravur">78- Paravur</option>
                        <option value="79- Vypeen">79- Vypeen</option>
                        <option value="80- Kochi">80- Kochi</option>
                        <option value="81- Tripunithura">81- Tripunithura</option>
                        <option value="82- Ernakulam">82- Ernakulam</option>
                        <option value="83- Thrikkakara">83- Thrikkakara</option>
                    </optgroup>
                
                    <optgroup label="Idukki">
                        <option value="86- Muvattupuzha">86- Muvattupuzha</option>
                        <option value="87- Kothamangalam">87- Kothamangalam</option>
                        <option value="88- Devikulam (SC)">88- Devikulam (SC)</option>
                        <option value="89- Udumbanchola">89- Udumbanchola</option>
                        <option value="90- Thodupuzha">90- Thodupuzha</option>
                        <option value="91- Idukki">91- Idukki</option>
                        <option value="92- Peerumade">92- Peerumade</option>
                    </optgroup>
                
                    <optgroup label="Kottayam">
                        <option value="85- Piravom">85- Piravom</option>
                        <option value="93- Pala">93- Pala</option>
                        <option value="94- Kaduthuruthy">94- Kaduthuruthy</option>
                        <option value="95- Vaikom (SC)">95- Vaikom (SC)</option>
                        <option value="96- Ettumanoor">96- Ettumanoor</option>
                        <option value="97- Kottayam">97- Kottayam</option>
                        <option value="98- Puthuppally">98- Puthuppally</option>
                    </optgroup>
                
                    <optgroup label="Alappuzha">
                        <option value="102- Aroor">102- Aroor</option>
                        <option value="103- Cherthala">103- Cherthala</option>
                        <option value="Alappuzha">104- Alappuzha</option>
                        <option value="105- Ambalappuzha">105- Ambalappuzha</option>
                        <option value="107- Haripad">107- Haripad</option>
                        <option value="108- Kayamkulam">108- Kayamkulam</option>
                        <option value="116- Karunagappally">116- Karunagappally</option>
                    </optgroup>
                
                    <optgroup label="Mavelikkara (SC)">
                        <option value="99- Changanassery">99- Changanassery</option>
                        <option value="106- Kuttanad">106- Kuttanad</option>
                        <option value="109- Mavelikkara (SC)">109- Mavelikkara (SC)</option>
                        <option value="110- Chengannur">110- Chengannur</option>
                        <option value="118- Kunnathur (SC)">118- Kunnathur (SC)</option>
                        <option value="119- Kottarakkara">119- Kottarakkara</option>
                        <option value="120- Pathanapuram">120- Pathanapuram</option>
                    </optgroup>
                
                    <optgroup label="Pathanamthitta">
                        <option value="100- Kanjirappally">100- Kanjirappally</option>
                        <option value="101- Poonjar">101- Poonjar</option>
                        <option value="111- Thiruvalla">111- Thiruvalla</option>
                        <option value="112- Ranni">112- Ranni</option>
                        <option value="113- Aranmula">113- Aranmula</option>
                        <option value="114- Konni">114- Konni</option>
                        <option value="115- Adoor (SC)">115- Adoor (SC)</option>
                    </optgroup>
                
                    <optgroup label="Kollam">
                        <option value="117- Chavara">117- Chavara</option>
                        <option value="121- Punalur">121- Punalur</option>
                        <option value="122- Chadayamangalam">122- Chadayamangalam</option>
                        <option value="123- Kundara">123- Kundara</option>
                        <option value="124- Kollam">124- Kollam</option>
                        <option value="125- Eravipuram">125- Eravipuram</option>
                        <option value="126- Chathannoor">126- Chathannoor</option>
                    </optgroup>
                
                    <optgroup label="Attingal">
                        <option value="127- Varkala">127- Varkala</option>
                        <option value="128- Attingal (SC)">128- Attingal (SC)</option>
                        <option value="129- Chirayinkeezhu (SC)">129- Chirayinkeezhu (SC)</option>
                        <option value="130- Nedumangad">130- Nedumangad</option>
                        <option value="131- Vamanapuram">131- Vamanapuram</option>
                        <option value="136- Aruvikkara">136- Aruvikkara</option>
                        <option value="138- Kattakkada">138- Kattakkada</option>
                    </optgroup>
                
                    <optgroup label="Thiruvananthapuram">
                        <option value="132- Kazhakkoottam">132- Kazhakkoottam</option>
                        <option value="133- Vattiyoorkavu">133- Vattiyoorkavu</option>
                        <option value="134- Thiruvananthapuram">134- Thiruvananthapuram</option>
                        <option value="135- Nemom">135- Nemom</option>
                        <option value="137- Parassala">137- Parassala</option>
                        <option value="139- Kovalam">139- Kovalam</option>
                        <option value="140- Neyyattinkara">140- Neyyattinkara</option>
                    </optgroup>
                </select>
                    

                <p>Or select your constituency by clicking on the India map:</p>
                <img src="india_map.png" alt="India Map" class="map"> 
              

                <button type="button" class="prev-btn">Previous</button>
                <button type="button" class="next-btn">Next</button>
            </div>

          
            <div class="form-section" id="section-5" style="display:none;">
                <h3>5. Family/Relation Details</h3>
                
                <label for="relation_type">Relation Type:</label>
                <select id="relation_type" name="relation_type" required>
                    <option value="">Select</option>
                    <option value="father">Father</option>
                    <option value="mother">Mother</option>
                    <option value="husband">Husband</option>
                    <option value="wife">Wife</option>
                </select>

                <label for="relation_name">Name of Relation (Father/Mother/Husband/Wife):</label>
                <input type="text" id="relation_name" name="relation_name" required>

                      <label for="relation_name">His/Her Voter id:</label>
                <input type="text" id="voter_id" name="voter_id" required>

                <button type="button" class="prev-btn">Previous</button>
                <button type="button" class="next-btn">Next</button>
            </div>

         
            <div class="form-section" id="section-6" style="display:none;">
                <h3>5. Declaration</h3>
                <p>I hereby declare that the above information is true and correct to the best of my knowledge.</p>

                <label for="declaration_date">Date of Declaration (DD/MM/YYYY):</label>
                <input type="date" id="declaration_date" name="declaration_date" placeholder="DD/MM/YYYY" required>

                <label for="applicant_signature">Place:</label>
                <input type="text" id="applicant_signature" name="applicant_signature" required>

                <button type="button" class="prev-btn">Previous</button>
                <button type="submit">Submit Form-A</button>
            </div>
            
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>

<script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentSection = 0;
            const sections = document.querySelectorAll(".form-section");

            function showSection(index) {
                sections.forEach((section, i) => {
                    section.style.display = i === index ? "block" : "none";
                });
            }

            document.querySelectorAll(".next-btn").forEach(button => {
                button.addEventListener("click", function() {
                    if (currentSection < sections.length - 1) {
                        currentSection++;
                        showSection(currentSection);
                    }
                });
            });

            document.querySelectorAll(".prev-btn").forEach(button => {
                button.addEventListener("click", function() {
                    if (currentSection > 0) {
                        currentSection--;
                        showSection(currentSection);
                    }
                });
            });

            const form = document.getElementById("voterForm");
            const submitButton = document.getElementById("submitButton");

            submitButton.addEventListener("click", function(event) {
                event.preventDefault(); 
                if (confirm("Are you sure you want to submit the form?")) {
                    form.submit(); 
                }
            });

            showSection(currentSection); 
        });
    </script>


</body>
</html>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 0;
    }
    
    header {
        background-color: #003366;
        color: white;
        text-align: center;
        padding: 20px;
    }
    
    h1, h2 {
        margin: 0;
        font-size: 24px;
    }
    
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    
    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    
    button {
        padding: 10px 15px;
        background-color: #003366;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    
    button:hover {
        background-color: #002244;
    }
    
    .map {
        width: 100%;
        height: auto;
        margin: 20px 0;
    }
    
    footer {
        text-align: center;
        padding: 20px;
        background-color: #003366;
        color: white;
        margin-top: 20px;
    }
    .logo{
        width:70px;
        height: 70px;
    }
    

</style>