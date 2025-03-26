<?php
curl.exe -X POST "https://verify.twilio.com/v2/Services/%YOUR_VERIFY_SID%/Verifications" ^
  --data-urlencode "To=+918590594735" ^
  --data-urlencode "Channel=sms" ^
  -u "ACb01a55ce17bdaf82e4aace6de42c6e72:9918e68038d218780ef7e2913a311773"

echo
echo -n "Please enter the OTP:"
read OTP_CODE

curl.exe -X POST "https://verify.twilio.com/v2/Services/%YOUR_VERIFY_SID%/VerificationCheck" ^
  --data-urlencode "To=+918590594735" ^
  --data-urlencode "Code=$OTP_CODE" ^
  -u "ACb01a55ce17bdaf82e4aace6de42c6e72:9918e68038d218780ef7e2913a311773"
  ?>