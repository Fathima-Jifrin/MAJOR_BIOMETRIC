<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aadhaar Data Collection</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    }

    .container {
        width: 40%;
        background: white;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-weight: bold;
        margin-top: 10px;
    }

    input, select, textarea {
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }

    textarea {
        height: 60px;
    }

    button {
        margin-top: 15px;
        padding: 10px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #0056b3;
    }

    </style>
</head>
<body>
    <div class="container">
        <h2>Aadhaar Data Collection Form</h2>
        <form action="submit.php" method="POST">
            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Aadhaar Number</label>
            <input type="text" name="aadhaar_number" pattern="\d{12}" title="Enter a 12-digit Aadhaar number" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone Number</label>
            <input type="text" name="phone_number" pattern="\d{10}" title="Enter a 10-digit phone number" required>

            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" required>

            <label>Address</label>
            <textarea name="address" required></textarea>

            <label>Landmark</label>
            <input type="text" name="landmark" required>

            <label>Pincode</label>
            <input type="text" name="pincode" pattern="\d{6}" title="Enter a 6-digit pincode" required>

            <label>State</label>
            <select name="state" required>
                <option value="">Select State</option>
                <option value="Andhra Pradesh">Andhra Pradesh</option>
                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                <option value="Assam">Assam</option>
                <option value="Bihar">Bihar</option>
                <option value="Chhattisgarh">Chhattisgarh</option>
                <option value="Delhi">Delhi</option>
                <option value="Goa">Goa</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Haryana">Haryana</option>
                <option value="Himachal Pradesh">Himachal Pradesh</option>
                <option value="Jharkhand">Jharkhand</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Kerala">Kerala</option>
                <option value="Madhya Pradesh">Madhya Pradesh</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Manipur">Manipur</option>
                <option value="Meghalaya">Meghalaya</option>
                <option value="Mizoram">Mizoram</option>
                <option value="Nagaland">Nagaland</option>
                <option value="Odisha">Odisha</option>
                <option value="Punjab">Punjab</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Sikkim">Sikkim</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Telangana">Telangana</option>
                <option value="Tripura">Tripura</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="Uttarakhand">Uttarakhand</option>
                <option value="West Bengal">West Bengal</option>
            </select>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
